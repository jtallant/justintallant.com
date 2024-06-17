<?php

/** @var \Laravel\Lumen\Routing\Router $router */

# =========================
# JSON ROUTES
# =========================
$router->group(['prefix' => 'api/comments'], function () use ($router) {

    $router->get('/', 'JustinTallant\Comments\CommentsController@index');

    $router->post('/', 'JustinTallant\Comments\CommentsController@store');

    $router->post('send-email-verification', 'JustinTallant\Comments\SendEmailVerificationController@store');

    $router->post('verify-comments-token', 'JustinTallant\Comments\VerifyCommentsTokenController@show');
});

# =========================
# HTML ROUTES
# =========================
$router->group(['prefix' => 'comments/email-verification'], function () use ($router) {

    $router->get('/', 'JustinTallant\Comments\EmailVerificationController@show');

});
