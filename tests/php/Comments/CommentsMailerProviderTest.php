<?php

use JustinTallant\Tests\TestCase;
use JustinTallant\Comments\MailgunMailer;
use JustinTallant\Comments\MailerInterface;

class CommentsMailerProviderTest extends TestCase
{
    public function testMailgunMailerIsBoundToMailerInterface()
    {
        // Assert that the MailerInterface is bound in the service container
        // and an instance of MailgunMailer is returned when resolved.
        $this->assertInstanceOf(
            MailgunMailer::class,
            $this->app->make(MailerInterface::class)
        );
    }
}