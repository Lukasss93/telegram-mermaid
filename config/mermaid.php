<?php

return [
    'baseurl' => env('MERMAID_BASEURL', 'http://localhost:8087/'),
    'tag' => env('MERMAID_TAG', '#mermaid'),

    'inline'=>[
        'max_chars' => 250,
        'cache_time' => (int)env('MERMAID_INLINE_CACHE_TIME', 60 * 60 * 24),
    ]
];
