<?php

declare(strict_types=1);

namespace JustinTallant\Comments\AI;

use OpenAI\Client;

class GptClient
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a chat session with the given parameters.
     *
     * @param array{
     *     model: string,
     *     messages: array<array{role: string, content: string}>
     * } $params The parameters for creating the chat session.
     * @return \ArrayAccess The response from the chat creation.
     */
    public function createChat(array $params): \ArrayAccess
    {
        return $this->client->chat()->create($params);
    }
}
