<?php

return [
    /**
     * Configs for Bol Control
     * There is no token for Bol bcs it has exp duration
     */
    'base_url' => env('BOl_BASE_URL'),
    'basic_auth_credits' => [
        'clientId' => '84d3e20f-eca2-41d6-80bb-8bb270428461',
        'clientSecret' => 'qH)4t!0b}GHb#Tc!uA4Y,EMaHhFO(XJrm0jYseXn2]q4YgE^)CW}cCSXp*WQIePL',
    ],
    'headers' => [
        'Accept' => 'application/vnd.retailer.v8+json',
        'Content-Type' => 'N/A',
    ],
];
