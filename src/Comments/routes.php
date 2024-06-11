<?php

use Illuminate\Http\Request;
use JustinTallant\Comments\CommentsController;

$router->group(['prefix' => 'api/comments'], function () use ($router) {

    $router->get('/', 'JustinTallant\Comments\CommentsController@index');

    $router->post('/', 'JustinTallant\Comments\CommentsController@store');
});

$router->group(['prefix' => 'comments/email-verification'], function () use ($router) {

    $router->get('/', 'JustinTallant\Comments\EmailVerificationController@show');

    // $router->post('/', 'JustinTallant\Comments\CommentsController@store');
});
