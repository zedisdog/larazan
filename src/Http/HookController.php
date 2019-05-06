<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 18-4-28
 * Time: 上午9:50
 */

namespace Dezsidog\Larazan\Http;


use Dezsidog\Larazan\Events\ReceivedYzMessage;
use Dezsidog\Larazan\Message\MessageFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HookController extends Controller
{
    /**
     * @param Request $request
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __invoke(Request $request)
    {
        $log = app()->make('log');
        $log->info('yz-message-receive', $request->input() ?? []);
        if ($request->has('type')) {
            $message = MessageFactory::create($request->input());
            if ($message) {
                event(new ReceivedYzMessage($message));
            }
        }

        $ret = '{"code":0,"msg":"success"}';
        if (\App::environment('testing')) {
            return $ret;
        } else {
            $log->info('yz-message-return', [$ret]);
            die($ret);
        }
    }
}