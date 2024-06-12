<?php

namespace JustinTallant\Comments\Tests;

use JustinTallant\Tests\TestCase;

class SendEmailVerificationControllerTest extends TestCase
{
    /** @test */
    public function it_sends_an_email_verification_request_to_provided_address()
    {
        $response = $this->call(
            'POST',
            '/api/comments/send-email-verification',
            ['email' => 'jtallant07@gmail.com']
        );

        $this->assertEquals(200, $response->status());

        $this->assertEquals('Verification email sent', $response->json('message'));
    }
}