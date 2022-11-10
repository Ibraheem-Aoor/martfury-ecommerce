<?php

return [
    /**
     * Configs for Bol Control
     */
    'base_url' => env('BOl_CONTROL_BASE_URL'),
    'token' => env('BOL_CONTROL_TOKEN'),
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ],
];
