<?php

namespace Tests\Comments\AI;

use Mockery;
use JustinTallant\Tests\TestCase;
use JustinTallant\Comments\AI\GptClient;
use JustinTallant\Comments\AI\GptCommentWriter;

class GptCommentWriterTest extends TestCase
{
    /** @test */
    public function it_writes_a_comment_response_using_open_ai_api()
    {
        $generatedResponse = 'Generated response content';

        $spyGptClient = Mockery::spy(GptClient::class);

        $spyGptClient->shouldReceive('createChat')
            ->andReturn([
                'choices' => [
                    ['message' => ['content' => $generatedResponse]]
                ]
            ]);

        $writer = new GptCommentWriter($spyGptClient);

        $response = $writer->write('prompt content', 'content replying to');

        $this->assertEquals($generatedResponse, $response);

        $createChat = [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'prompt content'],
                ['role' => 'user', 'content' => 'content replying to'],
            ],
        ];

        $spyGptClient->shouldHaveReceived('createChat')->once()->with($createChat);
    }
}
