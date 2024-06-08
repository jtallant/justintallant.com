<?php

use Illuminate\Http\Request;

$router->group(['prefix' => 'api/comments'], function () use ($router) {

    $router->get('/', function (Request $request) {
        return response()->json(
            app('comments')->getEntryComments($request->get('entry_uri'))
        );
    });

    $router->post('/', function (Request $request) {

        $allowedKeys = ['entry_uri', 'author', 'content'];

        $data = array_filter(
            $request->json()->all(),
            function ($key) use ($allowedKeys) {
                return in_array($key, $allowedKeys);
            },
            ARRAY_FILTER_USE_KEY
        );

        foreach ($data as $value) {
            if (!is_string($value)) {
                return response()->json(['message' => 'Invalid input'], 400);
            }
        }

        $data['is_author'] = $data['author'] === config('comments.author_secret');

        if ($data['is_author']) {
            $data['author'] = config('comments.author_name');
        }

        $newComment = app('comments')->createEntryComment($data);

        return response()
            ->json([
                'message' => 'Comment added successfully',
                'data' => $newComment,
            ], 201);
    });
});
