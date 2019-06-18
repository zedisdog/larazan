<?php
/**
 * Created by zed.
 */
return [
    'redirectUri' => '',
    'refreshTokenExpires' => 28*24*60*60,
    'clientId' => env('YZ_CLIENT_ID', ""),
    'clientSecret' => env('YZ_CLIENT_SECRET', ""),
    'kdtId' => env('YZ_KDT_ID', ""),
    'multiSeller' => true,
    'tag' => '',
    'hook' => [
        'prefix' => 'api',
        'middlewares' => 'api',
        'url' => 'youzan-hook',
        'action' => '\Dezsidog\Larazan\Http\HookController'
    ],
    'callback' => [
        'prefix' => 'api',
        'middlewares' => 'api',
        'url' => 'youzan-callback',
        'action' => '\Dezsidog\Larazan\Http\CallbackController'
    ],
    'ticket' => [
        'enabled' => env('YZ_TICKET_ENABLED', false),
        'notify' => [
            'prefix' => 'api',
            'middlewares' => 'api',
            'url' => 'yz-ticket-notify',
            'action' => '\Dezsidog\Larazan\Http\YzTicketNotifyController@notify'
        ],
        'compensate' => [
            'prefix' => 'api',
            'middlewares' => 'api',
            'url' => 'yz-ticket-compensate',
            'action' => '\Dezsidog\Larazan\Http\YzTicketNotifyController@compensate'
        ],
    ]
];