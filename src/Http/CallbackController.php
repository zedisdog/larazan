<?php
/**
 * Created by zed.
 */

namespace Dezsidog\LYouzanphp\Http;


use Dezsidog\LYouzanphp\Events\ReceivedYzCallbackMessage;
use Dezsidog\LYouzanphp\Events\ReceivedYzCode;
use Dezsidog\LYouzanphp\Events\ReceivedYzUserToken;
use Dezsidog\Youzanphp\Sec\Decrypter;
use Illuminate\Http\Request;

class CallbackController
{
    public function __invoke(Request $request)
    {
        if ($request->has('code')) {
            event(new ReceivedYzCode($request->input('code', '')));
        } elseif ($request->has('message')) {
            $decrypter = new Decrypter(config('larazan.clientSecret'));
            event(new ReceivedYzCallbackMessage($decrypter->decrypt($request->input('message'))));
        } elseif ($request->has('userToken')) {
            $decrypter = new Decrypter(config('larazan.clientSecret'));
            event(new ReceivedYzUserToken($decrypter->decrypt($request->input('userToken'))));
        }

        return ['success' => true];
    }
}