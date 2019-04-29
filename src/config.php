<?php
/**
 * Created by zed.
 */
return [
    'redirectUri' => '',
    'refreshTokenExpires' => 28*24*60*60,
    'clients' => [
        'default' => [
            'clientId' => env('YZ_CLIENT_ID', ""),
            'clientSecret' => env('YZ_CLIENT_SECRET', ""),
        ]
    ],
    'kdtId' => env('YZ_KDT_ID', ""),
    'multiSeller' => true,
    'hook' => [
        'prefix' => 'api',
        'middlewares' => 'api',
        'url' => 'youzan-hook',
        'action' => '\Dezsidog\LYouzanphp\Http\HookController'
    ],
    'callback' => [
        'prefix' => 'api',
        'middlewares' => 'api',
        'url' => 'youzan-callback',
        'action' => '\Dezsidog\LYouzanphp\Http\CallbackController'
    ],
];