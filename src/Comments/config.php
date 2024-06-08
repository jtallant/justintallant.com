<?php

$commentsDb = '/Users/jtallant/Sites/justintallant.com/database/comments.sqlite';

return [
    'driver' => 'sqlite',
    'database' => env('COMMENTS_DB_FILE_PATH', $commentsDb),
    'prefix' => '',
];
