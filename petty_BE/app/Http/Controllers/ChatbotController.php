<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatbotMessageRequest;
use App\Models\KhachHang;
use App\Services\ChatbotContextService;
use App\Services\ChatbotFunctionService;
use Carbon\Carbon;
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
        $defaultModel = config('services.groq.model', 'meta-llama/llama-4-scout-17b-16e-instruct');

        Log::info('Chatbot request received', [
            'trace_id' => $traceId,
            'has_api_key' => ! empty($apiKey),
        ]);

        if (! $apiKey) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa cấu hình GROQ API key ở backend.',
            ], 500);
        }

        $user = $this->resolveCustomer($request);

        $validated = $request->validated();
        $history = collect($validated['history'] ?? [])
            ->map(function ($item) {
                $historyImages = collect($item['images'] ?? [])
                    ->map(fn ($imageData) => ['type' => 'image_url', 'image_url' => ['url' => $imageData]])
                    ->values()
                    ->all();

                if (! empty($historyImages)) {
                    $content = [];
                    if (! empty($item['content'])) {
                        $content[] = ['type' => 'text', 'text' => $item['content']];
                    }
                    $content = array_merge($content, $historyImages);

                    return [
                        'role' => $item['role'],
                        'content' => $content,
                    ];
                }

                return [
                    'role' => $item['role'],
                    'content' => $item['content'],
                ];
            })
            ->values()
            ->all();

        $images = collect($validated['images'] ?? [])
            ->map(fn ($imageData) => ['type' => 'image_url', 'image_url' => ['url' => $imageData]])
            ->values()
            ->all();

        $historyHasImages = collect($validated['history'] ?? [])->contains(
            fn ($item) => ! empty($item['images'])
        );
        $hasImages = ! empty($images);
        $hasAnyImageContext = $hasImages || $historyHasImages;
        $model = $hasImages
            ? config('services.groq.vision_model', $defaultModel)
            : $defaultModel;
        if ($historyHasImages && ! $hasImages) {
            $model = config('services.groq.vision_model', $defaultModel);
        }
        $systemPrompt = $this->buildSystemPrompt($user, $hasAnyImageContext);

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
            'has_images' => $hasImages,
            'history_has_images' => $historyHasImages,
            'model' => $model,
            'message_count' => count($messages),
        ]);

        try {
            $result = $this->callAIWithFunctions($messages, $user, $model, $apiKey, $traceId, $userText);

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

    private function buildSystemPrompt(?KhachHang $user, bool $hasImages = false): string
    {
        $timeContext = $this->buildTimeContext();

        $base = 'Bạn là trợ lý tư vấn chăm sóc thú cưng (chó, mèo và thú cưng phổ biến) của phòng khám thú y Petty. '
            . 'Chỉ trả lời về chăm sóc thú cưng: dinh dưỡng, vệ sinh, hành vi, huấn luyện cơ bản, môi trường sống, dấu hiệu sức khỏe cần lưu ý. '
            . 'Nếu câu hỏi không liên quan thú cưng, từ chối lịch sự và nhắc người dùng hỏi đúng chủ đề. '
            . 'Không đưa chẩn đoán y khoa chắc chắn; với dấu hiệu nghiêm trọng hãy khuyên đi bác sĩ thú y. '
            . 'Trả lời ngắn gọn, dễ hiểu, có bước hành động cụ thể. '
            . 'CHỈ thêm câu "AI chỉ mang tính tham khảo, không thay thế bác sĩ thú y." khi câu trả lời có nội dung tư vấn thú y, đánh giá triệu chứng, chẩn đoán sơ bộ, điều trị, thuốc, mức độ nguy hiểm, hoặc nhận định tình trạng sức khỏe từ mô tả/ảnh. '
            . 'KHÔNG thêm câu này cho các câu trả lời tác vụ hoặc thông tin như: ngày tháng, giờ giấc, khung giờ trống, danh sách dịch vụ, thông tin thú cưng, lịch sử khám, lịch hẹn, đặt lịch thành công, hoặc các câu hỏi vận hành hệ thống.'
            . "\n\n"
            . $timeContext
            . "\n"
            . 'Khi người dùng dùng thời gian tương đối như "hôm nay", "ngày mai", "chiều mai", "sáng thứ 2", "cuối tuần này", hãy tự quy đổi sang ngày cụ thể dựa trên mốc thời gian ở trên. '
            . 'Nếu người dùng đã nói thời gian tương đối đủ rõ để xác định ngày, KHÔNG được hỏi lại ngày tháng cụ thể. '
            . 'Chỉ hỏi lại khi còn thiếu thông tin thực sự cần thiết, ví dụ thiếu giờ cụ thể hoặc thiếu tên thú cưng.'
            . "\n"
            . 'Khi người dùng hỏi còn slot ngày nào hoặc muốn đặt lịch bằng từ thời gian tương đối, hãy tự đổi sang định dạng YYYY-MM-DD hoặc YYYY-MM-DD HH:00 để gọi tool.'
            . "\n"
            . 'Nếu người dùng gửi ảnh, bạn phải phân tích trực tiếp nội dung ảnh đó. '
            . 'Nếu ảnh mờ hoặc không đủ rõ, hãy nói rõ giới hạn quan sát thay vì nói chung chung rằng bạn không xem được ảnh.';

        if ($hasImages) {
            $base .= "\n"
                . 'Trong lượt chat này có ảnh đính kèm. Hãy ưu tiên mô tả ngắn gọn những gì bạn nhìn thấy trong ảnh trước khi tư vấn.';
        }

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
            . 'QUAN TRỌNG: KHÔNG được tự ý chọn giờ khám nếu khách mới nói mơ hồ như "sáng mai", "chiều mai", "ngày mai", "cuối tuần này". '
            . 'Nếu khách chưa nêu giờ cụ thể, hãy kiểm tra slot trống rồi hỏi khách chọn một giờ chính xác trước khi gọi tool book_appointment. '
            . 'Thay vào đó hãy gọi tool get_services để tự tra cứu ID tương ứng với tên dịch vụ khách yêu cầu. '
            . 'Chỉ hỏi khách nếu thực sự không xác định được dịch vụ nào phù hợp. '
            . 'Nếu khách hỏi về thú cưng của họ, hãy dùng thông tin bên dưới để trả lời trực tiếp thay vì gọi tool (trừ khi cần thông tin mới nhất).';

        return $base . $functionGuidance . "\n\n" . $context;
    }

    private function buildTimeContext(): string
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $now = Carbon::now($timezone);
        $today = $now->copy()->startOfDay();
        $tomorrow = $today->copy()->addDay();
        $dayAfterTomorrow = $today->copy()->addDays(2);

        return sprintf(
            'MỐC THỜI GIAN HIỆN TẠI: Bây giờ là %s (%s). Hôm nay là %s. Ngày mai là %s. Ngày kia là %s.',
            $now->format('d/m/Y H:i'),
            $timezone,
            $today->format('Y-m-d'),
            $tomorrow->format('Y-m-d'),
            $dayAfterTomorrow->format('Y-m-d'),
        );
    }

    private function callAIWithFunctions(
        array $messages,
        ?KhachHang $user,
        string $model,
        string $apiKey,
        string $traceId,
        string $latestUserText = '',
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
                $error = $response->json('error.message') ?: ('Groq API Error: ' . $response->status());
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
                    if ($fnName === 'book_appointment' && ! $this->hasExplicitAppointmentTime($latestUserText)) {
                        $result = [
                            'error' => 'Khách chưa cung cấp giờ cụ thể để đặt lịch. Hãy hỏi lại khách chọn một giờ chính xác như 09:00, 14:00 hoặc đưa ra các slot trống để khách chọn.',
                        ];
                    } else {
                        $result = $this->functionService->executeFunction($fnName, $fnArgs, $user);
                    }
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

    private function hasExplicitAppointmentTime(string $text): bool
    {
        $normalized = mb_strtolower(trim($text));

        if ($normalized === '') {
            return false;
        }

        // Accept explicit hour forms like 14h, 14:00, 2 giờ, 2h30, 09h00.
        if (preg_match('/\b([01]?\d|2[0-3])\s*(h|g(?:i(?:ờ)?)?)\s*([0-5]\d)?\b/u', $normalized)) {
            return true;
        }

        if (preg_match('/\b([01]?\d|2[0-3]):([0-5]\d)\b/u', $normalized)) {
            return true;
        }

        return false;
    }

    private function mapFunctionToActionType(string $fnName): string
    {
        return match ($fnName) {
            'book_appointment' => 'appointment_booked',
            default => $fnName . '_result',
        };
    }
}
