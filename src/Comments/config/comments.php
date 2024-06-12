<?php

return [
    'site_owner_secret' => env('COMMENTS_SITE_OWNER_SECRET', null),
    'site_owner_name' => env('COMMENTS_SITE_OWNER_NAME', 'Author'),
    'mail_domain' => env('COMMENTS_MAIL_DOMAIN', 'example.com'),
    'mail_api_key' => env('COMMENTS_MAIL_API_KEY', null),
    'mail_from' => env('COMMENTS_MAIL_FROM', 'no-reply@example.com'),
    'mail_subject' => 'Please verify your email address',
];