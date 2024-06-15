<?php

namespace JustinTallant\Comments\AI;

use OpenAI;
use Illuminate\Support\ServiceProvider;
use JustinTallant\Comments\AI\GptCommentWriter;
use JustinTallant\Comments\AI\CommentWriterInterface;
use JustinTallant\Comments\AI\CreateAgentCommentReplies;

class CommentWriterProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GptCommentWriter::class, function ($app) {
            $apiKey = config('comments.openai_api_key');
            $client = new GptClient(OpenAI::client($apiKey));
            return new GptCommentWriter($client);
        });

        $this->app->bind(CommentWriterInterface::class, GptCommentWriter::class);

        $this->app->singleton(CreateAgentCommentReplies::class, function ($app) {
            return new CreateAgentCommentReplies($app->make('registry'), config('comments.prompts'));
        });

        $this->commands([
            CreateAgentCommentReplies::class,
        ]);
    }
}