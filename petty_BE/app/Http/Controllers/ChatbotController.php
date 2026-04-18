<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatbotMessageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function message(ChatbotMessageRequest $request): JsonResponse
    {
        $traceId = (string) Str::uuid();
        $startedAt = microtime(true);

        $apiKey = config('services.together.api_key');
        $model = config('services.together.model', 'meta-llama/Llama-4-Maverick-17B-128E-Instruct-FP8');

        Log::info('Chatbot request received', [
            'trace_id' => $traceId,
            'model' => $model,
            'has_api_key' => ! empty($apiKey),
        ]);

        if (! $apiKey) {
            Log::error('Chatbot missing API key', [
                'trace_id' => $traceId,
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Chưa cấu hình Together API key ở backend.',
            ], 500);
        }

        $validated = $request->validated();
        $history = collect($validated['history'] ?? [])
            ->map(function ($item) {
                return [
                    'role' => $item['role'],
                    'content' => $item['content'],
                ];
            })
            ->values()
            ->all();

        $images = collect($validated['images'] ?? [])
            ->map(function ($imageData) {
                return [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => $imageData,
                    ],
                ];
            })
            ->values()
            ->all();

        $systemPrompt = 'Bạn là trợ lý tư vấn chăm sóc thú cưng (chó, mèo và thú cưng phổ biến). '
            . 'Chỉ trả lời về chăm sóc thú cưng: dinh dưỡng, vệ sinh, hành vi, huấn luyện cơ bản, môi trường sống, dấu hiệu sức khỏe cần lưu ý. '
            . 'Nếu câu hỏi không liên quan thú cưng, từ chối lịch sự và nhắc người dùng hỏi đúng chủ đề. '
            . 'Không đưa chẩn đoán y khoa chắc chắn; với dấu hiệu nghiêm trọng hãy khuyên đi bác sĩ thú y. '
            . 'Trả lời ngắn gọn, dễ hiểu, có bước hành động cụ thể.';

        $userText = trim((string) ($validated['message'] ?? ''));
        $userContent = [];

        if ($userText !== '') {
            $userContent[] = [
                'type' => 'text',
                'text' => $userText,
            ];
        }

        if (! empty($images)) {
            $userContent = array_merge($userContent, $images);
        }

        if (empty($userContent)) {
            $userContent[] = [
                'type' => 'text',
                'text' => 'Nhờ bạn xem ảnh thú cưng này giúp mình.',
            ];
        }

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $history,
            [['role' => 'user', 'content' => $userContent]]
        );

        Log::debug('Chatbot payload prepared', [
            'trace_id' => $traceId,
            'message_length' => mb_strlen($userText),
            'history_count' => count($history),
            'image_count' => count($images),
            'message_count' => count($messages),
        ]);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.together.xyz/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                ]);

            $elapsedMs = (int) ((microtime(true) - $startedAt) * 1000);

            if (! $response->ok()) {
                $error = $response->json('error.message') ?: ('Together API Error: ' . $response->status());

                Log::error('Chatbot upstream returned non-OK response', [
                    'trace_id' => $traceId,
                    'http_status' => $response->status(),
                    'elapsed_ms' => $elapsedMs,
                    'response_body' => $response->body(),
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $error,
                ], 502);
            }

            $reply = data_get($response->json(), 'choices.0.message.content', 'Không có nội dung phản hồi.');

            Log::info('Chatbot reply generated', [
                'trace_id' => $traceId,
                'http_status' => $response->status(),
                'elapsed_ms' => $elapsedMs,
                'reply_length' => mb_strlen((string) $reply),
            ]);

            return response()->json([
                'status' => true,
                'reply' => $reply,
            ]);
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
}
