<?php

require_once __DIR__.'/../vendor/autoload.php';

$site = new \Skimpy\Site(__DIR__);

$app = $site->bootstrap();

return $app->run();

