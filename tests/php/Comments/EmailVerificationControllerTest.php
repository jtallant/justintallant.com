<?php

namespace Tests\JustinTallant\Comments;

use JustinTallant\Tests\TestCase;
use JustinTallant\Comments\Entities\Email;

class EmailVerificationControllerTest extends TestCase
{
    /** @test */
    public function it_returns_400_if_token_is_invalid(): void
    {
        $response = $this->call(
            'GET',
            '/comments/email-verification',
            ['token' => 'invalid_token']
        );

        $this->assertEquals('Invalid token', $response->getContent());

        $response->assertStatus(400);
    }

    /** @test */
    public function it_returns_success_if_token_is_valid(): void
    {
        $email = new Email('Justin Tallant', 'test@test.com', 'example-entry-uri');
        $this->em->persist($email);
        $this->em->flush();

        $response = $this->call(
            'GET',
            '/comments/email-verification',
            ['token' => $email->token()]
        );

        $response->assertStatus(200);
    }
}
