<?php

namespace Dezsidog\Larazan\Http;

use Dezsidog\Larazan\Events\ReceivedYzTicketCompensateMessage;
use Dezsidog\Larazan\Events\ReceivedYzTicketMessage;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TicketNotifyController extends Controller
{
    /**
     * 处理有赞门票交易推送
     * @param Request $request
     * @return false|string
     */
    public function notify(Request $request)
    {
        Log::info('order.request', $request->input());

        event(new ReceivedYzTicketMessage($request->input()));

        return json_encode([
            'success' => true
        ]);
    }

    /**
     * 处理有赞门票补偿请求
     * @param Request $request
     * @return false|string
     */
    public function compensate(Request $request)
    {
        Log::info('order.compensate', $request->input());

        $result = event(new ReceivedYzTicketCompensateMessage($request->input('orderNo')));

        $last = array_pop($result);

        if(is_string($last)) {
            $res = $last;
        } elseif (is_array($last)) {
            $res = json_encode($last);
        } else {
            $res = json_encode([
                'success' => false
            ]);
        }

        Log::info('order.compensate.result', [$res]);

        return $res;
    }
}
