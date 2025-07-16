<?php

return [
    'name' => 'CodeAnalyzer',
    'api_uri' => env('CODEANALYZER_API_URI', false),
    'gh_uri' => env('GITHUB_API_URI', false),
    'gh_key' => env('GITHUB_API_KEY', false),
    'rabbitmq_host' => env('RABBITMQ_HOST', 'localhost'),
    'rabbitmq_port' => env('RABBITMQ_PORT', '5672'),
    'rabbitmq_username' => env('RABBITMQ_USERNAME', 'guest'),
    'rabbitmq_password' => env('RABBITMQ_PASSWORD', 'guest'),
    'rabbitmq_queue' => env('RABBITMQ_QUEUE', 'jobs'),
];
