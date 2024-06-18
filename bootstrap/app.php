<?php

require_once __DIR__ . '/../vendor/autoload.php';

$site = new \Skimpy\Site(__DIR__);

/** @var \Laravel\Lumen\Application $app */
$app = $site->bootstrap();

# Register your own service providers here
$app->register(\JustinTallant\HelloWorldProvider::class);
$app->register(\JustinTallant\Comments\CommentsServiceProvider::class);
$app->register(\JustinTallant\Comments\TwigFunctionsProvider::class);
$app->register(\JustinTallant\Comments\CommentsMailerProvider::class);
$app->register(\JustinTallant\Comments\AI\CommentWriterProvider::class);

# This has to come last because of the greedy routes
$app->register(\Skimpy\Lumen\Providers\SkimpyRouteProvider::class);

return $app;