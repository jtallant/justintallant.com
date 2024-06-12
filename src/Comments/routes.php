<?php

$router->group(['prefix' => 'api/comments'], function () use ($router) {

    $router->get('/', 'JustinTallant\Comments\CommentsController@index');

    $router->post('/', 'JustinTallant\Comments\CommentsController@store');
});

$router->group(['prefix' => 'comments/email-verification'], function () use ($router) {

    $router->get('/', 'JustinTallant\Comments\EmailVerificationController@show');

});

$router->group(['prefix' => 'api/comments/send-email-verification'], function () use ($router) {

    $router->post('/', 'JustinTallant\Comments\SendEmailVerificationController@store');

});