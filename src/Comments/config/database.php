<?php

return [
    'connections' => [
        'comments' => [
            'paths' => [
                base_path('src/Comments/Entities')
            ],
            'dev' => env('APP_ENV') !== 'production',
            'driver' => 'sqlite',
            'database' => env('COMMENTS_DB_FILE_PATH', base_path('database/comments.sqlite')),
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
    ],
];
