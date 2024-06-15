<?php

namespace JustinTallant\Comments\AI;

use OpenAI\Client;

class GptClient
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function createChat(array $params)
    {
        return $this->client->chat()->create($params);
    }
}
