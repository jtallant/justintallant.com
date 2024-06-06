<?php

require_once __DIR__.'/../vendor/autoload.php';

$site = new \Skimpy\Site(__DIR__);

/** @var \Laravel\Lumen\Application $app */
$app = $site->bootstrap();

# Register your own service providers here
$app->register(\JustinTallant\HelloWorldProvider::class);

return $app->run();

