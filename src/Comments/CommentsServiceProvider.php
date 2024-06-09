<?php

declare(strict_types=1);

namespace JustinTallant\Comments;

use Illuminate\Support\ServiceProvider;

class CommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'comments');

        $this->app->singleton(CommentsRepository::class, function ($app) {
            return new CommentsRepository(new Database(config('comments'), $app['validator']));
        });

        $twig = $this->app->get('twig');

        $twig->addFunction(new \Twig\TwigFunction('comments', function () {
            return $this->app->get(CommentsRepository::class);
        }));
    }

    public function boot(): void
    {
        $router = $this->app->router;
        require __DIR__ . '/routes.php';
    }
}
