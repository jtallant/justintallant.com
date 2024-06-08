<?php

$commentsDb = '/Users/jtallant/Sites/justintallant.com/database/comments.sqlite';

return [
    'driver' => 'sqlite',
    'database' => env('COMMENTS_DB_FILE_PATH', $commentsDb),
    'prefix' => '',
    'author_secret' => env('COMMENTS_AUTHOR_SECRET', null),
    'author_name' => env('COMMENTS_AUTHOR_NAME', 'author'),
];
