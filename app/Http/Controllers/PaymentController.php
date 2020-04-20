<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order, Request $request)
    {
        // 判断订单是否属于当前用户
        $this->authorize('own', $order);

        // 订单已支付或已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        // 调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no' => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
            'subject' => '支付 Laravel Shop 订单：' . $order->no, // 订单标题
        ]);
    }

    // 前端回调页面
    public function alipayReturn()
    {
        // 校验提交的参数是否合法
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
        
    }

    // 服务器回调页面
    public function alipayNotify()
    {
        $data = app('alipay')->verify();
        /**
         * 返回的数据格式
         * {"gmt_create":"2020-04-19 23:52:30","charset":"utf-8","gmt_payment":"2020-04-19 23:55:48","notify_time":"2020-04-19 23:55:49","subject":"支付 Laravel Shop 订单：20200416085943516456","sign":"ILT3WA1iKOsdvy9jTUVpibjReieSogquSnXlYOrkiHx6VOh+AiwqJFabTm9ZTjVj9+jQHcppS28Rdh4xM7Epk68m6sSKskVfWu9RlhuOoQ75g0yn0NHZblzjYC5K8lFLzC89Ve4QFxKLW69F1oKW8dTHsSpMWpUfLe404y4zMWkR6wjdOsFZYEJmuY97coH9M7sjDdsBrJ+qC6klj0FC08s5I20KltHvjhMlxhx/uYQhJz+LQomU41Bv4VJ6Tw6G/bio15mclTOg8JWeiekn8Z9OycF+a9P4LWnunCw+dICKK11w5UwRImPXID9BuBCxGX5DQUbY/kRCWB+T//cEEA==","buyer_id":"2088102180738691","invoice_amount":"1.00","version":"1.0","notify_id":"2020041900222235549038690506159304","fund_bill_list":"[{\"amount\":\"1.00\",\"fundChannel\":\"ALIPAYACCOUNT\"}]","notify_type":"trade_status_sync","out_trade_no":"20200416085943516456","total_amount":"1.00","trade_status":"TRADE_SUCCESS","trade_no":"2020041922001438690501069385","auth_app_id":"2016102400752276","receipt_amount":"1.00","point_amount":"0.00","app_id":"2016102400752276","buyer_pay_amount":"1.00","sign_type":"RSA2","seller_id":"2088102180905934"}
         */
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有交易状态：https://docs.open.alipay.com/59/103672
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if (!$order) {
            return 'fail';
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
        ]);

        return app('alipay')->success();
    }
    
}
