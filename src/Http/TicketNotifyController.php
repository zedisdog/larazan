<?php

namespace Dezsidog\Larazan\Http;

use Dezsidog\Larazan\Events\ReceivedYzTicketCompensateMessage;
use Dezsidog\Larazan\Events\ReceivedYzTicketMessage;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class YzTicketNotifyController extends Controller
{
    /**
     * 处理有赞门票交易推送
     * @param Request $request
     * @return false|string
     */
    public function notify(Request $request)
    {
        \Log::info('order.request', $request->input());

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
        \Log::info('order.compensate', $request->input());

        event(new ReceivedYzTicketCompensateMessage($request->input('orderNo')));

        return json_encode([
            'success' => true
        ]);
    }
}
