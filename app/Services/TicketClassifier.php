<?php
namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class TicketClassifier
{
    public function classify(string $subject, string $body): array
    {
        if (!filter_var(env('OPENAI_CLASSIFY_ENABLED', false), FILTER_VALIDATE_BOOLEAN)) {
            // Dummy categories for offline/dev mode
            $cats = ['bug','billing','feature_request','account','other'];
            return [
                'category' => $cats[array_rand($cats)],
                'explanation' => 'Classification disabled; returning dummy result.',
                'confidence' => round(mt_rand(50, 95) / 100, 2),
            ];
        }

        $system = "You are a ticket classifier. Return **ONLY** JSON with keys: category (one of [bug, billing, feature_request, account, other]), explanation (short string), confidence (0-1 float).";
        $user = "Subject: {$subject}\nBody: {$body}";

        $resp = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        $json = $resp->choices[0]->message->content ?? '{}';
        $data = json_decode($json, true) ?: [];

        // Normalize
        $data['category'] = $data['category'] ?? 'other';
        $data['explanation'] = $data['explanation'] ?? '';
        $data['confidence'] = isset($data['confidence']) ? (float)$data['confidence'] : null;
        return $data;
    }
}