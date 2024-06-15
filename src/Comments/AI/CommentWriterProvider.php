<?php

namespace JustinTallant\Comments\AI;

use OpenAI;
use Illuminate\Support\ServiceProvider;

class CommentWriterProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GptCommentWriter::class, function ($app) {
            $apiKey = config('comments.openai_api_key');
            $client = new GptClient(OpenAI::client($apiKey));
            return new GptCommentWriter($client, config('comments.prompts'));
        });

        $this->app->bind(CommentWriterInterface::class, GptCommentWriter::class);

        $this->app->singleton(CreateAgentComments::class, function ($app) {
            return new CreateAgentComments($app->make('registry'), config('comments.prompts'));
        });

        $this->commands([
            CreateAgentComments::class,
        ]);
    }
}