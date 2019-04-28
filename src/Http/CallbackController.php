<?php
/**
 * Created by zed.
 */

namespace Dezsidog\LYouzanphp\Http;


use Dezsidog\LYouzanphp\Events\ReceivedYzSubMessage;
use Dezsidog\LYouzanphp\Events\ReceivedYzCode;
use Dezsidog\LYouzanphp\Events\ReceivedYzBindMessage;
use Dezsidog\Youzanphp\Sec\Decrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallbackController
{
    public function __invoke(Request $request)
    {
        $log = app()->make('log');
        $log->info('yz-callback-receive', $request->input() ?? []);
        if ($request->has('code')) {
            event(new ReceivedYzCode($request->input('code', '')));
        } elseif ($request->has('message')) {
            $decrypter = new Decrypter(config('larazan.clientSecret'));
            $message = $decrypter->decrypt($request->input('message'));
            switch ($message['type']) {
                case 'APP_SUBSCRIBE':
                    event(new ReceivedYzSubMessage($message));
                    break;
                case 'APP_AUTH':
                    event(new ReceivedYzBindMessage($message));
                    break;
                default:
                    Log::info('receive unknown message', $message);
            }
        }

        return ['success' => true];
    }
}