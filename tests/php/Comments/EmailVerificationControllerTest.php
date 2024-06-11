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
    public function it_shows_success_page_if_token_is_valid(): void
    {
        $email = new Email('Justin Tallant', 'test@test.com', 'example-entry-uri');
        $this->em->persist($email);
        $this->em->flush();

        $this->assertFalse($email->verified());

        $response = $this->call(
            'GET',
            '/comments/email-verification',
            ['token' => $email->token()]
        );

        $response
            ->assertStatus(200)
            ->assertSee('Email Verified');

        $this->assertTrue($email->verified());
    }
}
