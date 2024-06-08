<?php

declare(strict_types=1);

namespace JustinTallant\Comments;

use Illuminate\Support\ServiceProvider;

class CommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'comments');

        $this->app->singleton('comments', function ($app) {
            return new CommentsRepository(new Database(config('comments')));
        });

        $comments = $this->app['comments'];
    }

    public function boot(): void
    {
        $router = $this->app->router;
        require __DIR__ . '/routes.php';
    }
}
