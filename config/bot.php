<?php

return [
    'username' => env('BOT_USERNAME'),
    'privacy' => env('BOT_PRIVACY'),
    'channel' => env('BOT_CHANNEL'),
    'source' => env('BOT_SOURCE'),
    'changelog' => env('BOT_CHANGELOG'),

    'clear_inline_files' => env('BOT_CLEAR_INLINE_FILES', true),
];
