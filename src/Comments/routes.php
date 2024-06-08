<?php

use Illuminate\Http\Request;
use JustinTallant\Comments\CommentsController;

$router->group(['prefix' => 'api/comments'], function () use ($router) {

    $router->get('/', 'JustinTallant\Comments\CommentsController@index');

    $router->post('/', 'JustinTallant\Comments\CommentsController@store');
});
