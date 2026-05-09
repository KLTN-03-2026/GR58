<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatbotMessageRequest;
use App\Models\KhachHang;
use App\Services\ChatbotContextService;
use App\Services\ChatbotFunctionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    private const MAX_FUNCTION_ITERATIONS = 3;

    public function __construct(
        private ChatbotContextService $contextService,
        private ChatbotFunctionService $functionService,
    ) {}

    public function message(ChatbotMessageRequest $request): JsonResponse
    {
        $traceId = (string) Str::uuid();
        $startedAt = microtime(true);

        $apiKey = config('services.groq.api_key');
        $model = config('services.groq.model', 'meta-llama/llama-4-scout-17b-16e-instruct');

        Log::info('Chatbot request received', [
            'trace_id' => $traceId,
            'model' => $model,
            'has_api_key' => ! empty($apiKey),
        ]);

        if (! $apiKey) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa cấu hình Together API key ở backend.',
            ], 500);
        }

        $user = $this->resolveCustomer($request);

        $validated = $request->validated();
        $history = collect($validated['history'] ?? [])
            ->map(fn ($item) => ['role' => $item['role'], 'content' => $item['content']])
            ->values()
            ->all();

        $images = collect($validated['images'] ?? [])
            ->map(fn ($imageData) => ['type' => 'image_url', 'image_url' => ['url' => $imageData]])
            ->values()
            ->all();

        $systemPrompt = $this->buildSystemPrompt($user);

        $userText = trim((string) ($validated['message'] ?? ''));

        // Use array content only when images are present (vision format).
        // Plain text models (Kimi, Llama, etc.) reject array content with no images.
        if (! empty($images)) {
            $userContent = [];
            if ($userText !== '') {
                $userContent[] = ['type' => 'text', 'text' => $userText];
            }
            $userContent = array_merge($userContent, $images);
            if (empty($userContent)) {
                $userContent[] = ['type' => 'text', 'text' => 'Nhờ bạn xem ảnh thú cưng này giúp mình.'];
            }
        } else {
            $userContent = $userText !== '' ? $userText : 'Nhờ bạn xem ảnh thú cưng này giúp mình.';
        }

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $history,
            [['role' => 'user', 'content' => $userContent]]
        );

        Log::debug('Chatbot payload prepared', [
            'trace_id' => $traceId,
            'has_user' => $user !== null,
            'message_count' => count($messages),
            'user_content_types' => array_map(fn($item) => $item['type'] ?? 'unknown', $userContent),
        ]);

        try {
            $result = $this->callAIWithFunctions($messages, $user, $model, $apiKey, $traceId);

            $elapsedMs = (int) ((microtime(true) - $startedAt) * 1000);

            Log::info('Chatbot reply generated', [
                'trace_id' => $traceId,
                'elapsed_ms' => $elapsedMs,
                'reply_length' => mb_strlen((string) $result['reply']),
                'function_calls' => $result['function_calls_count'],
            ]);

            $response = [
                'status' => true,
                'reply' => $result['reply'],
            ];

            if (! empty($result['actions'])) {
                $response['actions'] = $result['actions'];
            }

            return response()->json($response);
        } catch (\Throwable $e) {
            Log::error('Chatbot message failed', [
                'trace_id' => $traceId,
                'message' => $e->getMessage(),
                'elapsed_ms' => (int) ((microtime(true) - $startedAt) * 1000),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Không thể kết nối chatbot lúc này. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    private function resolveCustomer($request): ?KhachHang
    {
        try {
            $user = $request->user('khach_hang');
            if ($user instanceof KhachHang) {
                return $user;
            }
        } catch (\Throwable $e) {
            // Token invalid or missing — treat as guest
        }

        return null;
    }

    private function buildSystemPrompt(?KhachHang $user): string
    {
        $base = 'Bạn là trợ lý tư vấn chăm sóc thú cưng (chó, mèo và thú cưng phổ biến) của phòng khám thú y Petty. '
            . 'Chỉ trả lời về chăm sóc thú cưng: dinh dưỡng, vệ sinh, hành vi, huấn luyện cơ bản, môi trường sống, dấu hiệu sức khỏe cần lưu ý. '
            . 'Nếu câu hỏi không liên quan thú cưng, từ chối lịch sự và nhắc người dùng hỏi đúng chủ đề. '
            . 'Không đưa chẩn đoán y khoa chắc chắn; với dấu hiệu nghiêm trọng hãy khuyên đi bác sĩ thú y. '
            . 'Trả lời ngắn gọn, dễ hiểu, có bước hành động cụ thể. '
            . 'Luôn nhắc: "AI chỉ mang tính tham khảo, không thay thế bác sĩ thú y."';

        if (! $user) {
            return $base;
        }

        $context = $this->contextService->buildContext($user);

        if ($context === '') {
            return $base;
        }

        $functionGuidance = "\n\n"
            . 'Bạn có thể sử dụng các công cụ (tools) để giúp khách hàng: '
            . 'xem thú cưng, xem lịch sử khám, xem lịch hẹn sắp tới, kiểm tra khung giờ trống, xem danh sách dịch vụ, và đặt lịch hẹn. '
            . 'QUAN TRỌNG: Khi khách hàng muốn đặt lịch, KHÔNG hỏi khách về ID dịch vụ. '
            . 'Thay vào đó hãy gọi tool get_services để tự tra cứu ID tương ứng với tên dịch vụ khách yêu cầu. '
            . 'Chỉ hỏi khách nếu thực sự không xác định được dịch vụ nào phù hợp. '
            . 'Nếu khách hỏi về thú cưng của họ, hãy dùng thông tin bên dưới để trả lời trực tiếp thay vì gọi tool (trừ khi cần thông tin mới nhất).';

        return $base . $functionGuidance . "\n\n" . $context;
    }

    private function callAIWithFunctions(
        array $messages,
        ?KhachHang $user,
        string $model,
        string $apiKey,
        string $traceId,
    ): array {
        $actions = [];
        $functionCallsCount = 0;
        $tools = ($user !== null) ? $this->functionService->getToolDefinitions() : null;

        for ($iteration = 0; $iteration <= self::MAX_FUNCTION_ITERATIONS; $iteration++) {
            $payload = [
                'model' => $model,
                'messages' => $messages,
            ];

            if ($tools && $iteration < self::MAX_FUNCTION_ITERATIONS) {
                $payload['tools'] = $tools;
                $payload['tool_choice'] = 'auto';
            }

            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.groq.com/openai/v1/chat/completions', $payload);

            if (! $response->ok()) {
                $error = $response->json('error.message') ?: ('Together API Error: ' . $response->status());
                throw new \RuntimeException($error);
            }

            $choice = data_get($response->json(), 'choices.0.message');
            $toolCalls = $choice['tool_calls'] ?? null;

            if (empty($toolCalls)) {
                $reply = $choice['content'] ?? 'Không có nội dung phản hồi.';
                return [
                    'reply' => $reply,
                    'actions' => $actions,
                    'function_calls_count' => $functionCallsCount,
                ];
            }

            if (! $user) {
                $reply = $choice['content'] ?? 'Không có nội dung phản hồi.';
                return [
                    'reply' => $reply,
                    'actions' => $actions,
                    'function_calls_count' => $functionCallsCount,
                ];
            }

            $messages[] = $choice;

            foreach ($toolCalls as $toolCall) {
                $functionCallsCount++;
                $fnName = $toolCall['function']['name'] ?? '';
                $fnArgs = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?: [];

                Log::debug('Chatbot function call', [
                    'trace_id' => $traceId,
                    'function' => $fnName,
                    'args' => $fnArgs,
                    'iteration' => $iteration,
                ]);

                try {
                    $result = $this->functionService->executeFunction($fnName, $fnArgs, $user);
                } catch (\Throwable $e) {
                    $result = ['error' => 'Lỗi khi thực hiện: ' . $e->getMessage()];
                }

                if (isset($result['success']) && $result['success']) {
                    $actions[] = [
                        'type' => $this->mapFunctionToActionType($fnName),
                        'data' => $result['appointment'] ?? $result,
                    ];
                }

                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $toolCall['id'] ?? '',
                    'content' => json_encode($result, JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        $lastContent = '';
        foreach (array_reverse($messages) as $msg) {
            if (($msg['role'] ?? '') === 'assistant' && ! empty($msg['content'])) {
                $lastContent = $msg['content'];
                break;
            }
        }

        return [
            'reply' => $lastContent ?: 'Xin lỗi, yêu cầu quá phức tạp. Vui lòng thử lại đơn giản hơn.',
            'actions' => $actions,
            'function_calls_count' => $functionCallsCount,
        ];
    }

    private function mapFunctionToActionType(string $fnName): string
    {
        return match ($fnName) {
            'book_appointment' => 'appointment_booked',
            default => $fnName . '_result',
        };
    }
}
