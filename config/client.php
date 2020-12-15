<?php

// client api configurations
return [
    'supermetrics' => [
        'url' => getenv('SUPERMETRICS_URL') ?: 'http://127.0.0.1:8000/',
        'clientId' => getenv('SUPERMETRICS_API_CLIENT_ID'),
        'email' => getenv('SUPERMETRICS_API_EMAIL'),
        'name' => getenv('SUPERMETRICS_API_NAME'),
    ]
];
