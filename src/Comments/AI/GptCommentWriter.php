<?php

declare(strict_types=1);

namespace JustinTallant\Comments\AI;

use JustinTallant\Comments\AI\GptClient;
use JustinTallant\Comments\AI\CommentWriterInterface;

class GptCommentWriter implements CommentWriterInterface
{
    private $gptClient;

    public function __construct(GptClient $gptClient)
    {
        $this->gptClient = $gptClient;
    }

    public function write(string $prompt, string $content): string
    {
        $systemMessage = [
            'role' => 'system',
            'content' => $prompt
        ];

        $userMessage = [
            'role' => 'user',
            'content' => $content
        ];

        try {
            $response = $this->gptClient->createChat([
                'model' => 'gpt-4o',
                'messages' => [$systemMessage, $userMessage]
            ]);

            return $response['choices'][0]['message']['content'];
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Failed to generate comment: ' . $e->getMessage());
        }
    }
}