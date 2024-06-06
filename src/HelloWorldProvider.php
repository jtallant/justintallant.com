<?php

namespace JustinTallant;

use Illuminate\Support\ServiceProvider;

class HelloWorldProvider extends ServiceProvider
{
    public function register()
    {
        $twig = $this->app->get('twig');

        $helloWorld = new \Twig\TwigFunction('helloWorld', function () {
            return 'Hello World!!';
        });

        $twig->addFunction($helloWorld);
    }
}

