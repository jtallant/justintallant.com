<?php

namespace JustinTallant\Comments;

use Mailgun\Mailgun;
use Illuminate\Support\ServiceProvider;

class CommentsMailerProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MailerInterface::class, function ($app) {

            $mailgun = Mailgun::create(config('comments.mail_api_key'));

            return new MailgunMailer(
                $mailgun, config('comments.mail_domain')
            );
        });
    }
}
