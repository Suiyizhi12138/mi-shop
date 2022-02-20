<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Exceptions\InvalidRequestException;
use App\Models\OrderItem;
use Yansongda\Pay\Exceptions\Exception;
use Illuminate\Support\Carbon;


class PaymentController extends Controller
{
    protected $order;
    public function payByAlipay(Order $order,Request $request){
        //判断订单是否属于当前用户
        //$this->authorize('own',$order);       
        //订单已支付或者已关闭
        if($order->paid_at || $order->closed){
            throw new InvalidRequestException('订单状态不正确');
        }

        //调用支付宝的网页支付
       return app('alipay')->web([
            'out_trade_no' => $order->no,//订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount,//订单金额 单位元 支持小数点后两位
            'subject' => '支付 mi-shop 的订单'.$order->no,//订单标题
        ]);
    }
    //前端回调页面
    public function alipayReturn(){
        //校验提交的参数是否合法
        try{
            app('alipay')->verify();
        } catch(Exception $e){
            return view('page.errors',[
                'msg' => '数据不正确'
            ]);
        }
        return view('page.success',['msg'=>'付款成功']);          
    }
    //服务器端回调
    public function alipayNotify(){
        //校验输入参数
        $data = app('alipay')->verify();
        //如果订单状态不是成功或者结束 则不走后续的逻辑
        if(!in_array($data->trade_status,['TRADE_SUCCESS','TRADE_FINISHED'])){
            return app('alipay')->success();
        }
        $order = Order::where('no',$data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if(!$order){
            return 'fail';
        }
        //若果这笔订单的状态已经是已支付
        if($order->paid_at){
            //返回数据给支付宝
            return app('alipay')->success();
        }
        $order->update(
            [
                'paid_at' => Carbon::now(),//支付时间
                'payment_method' => 'alipay',//支付方式
                'payment_no' => $data->trade_no,//支付宝订单号
            ]
        );
        $this->afterPaid($order);
        return app('alipay')->success();
    }
    //
    public function afterPaid(Order $order){
        event(new OrderPaid($order));
    }
    //调错用
    // public function updateSoldCount($no){
    //     $order = Order::where('no',$no)->first();
    //     $order->load('items.product');
    //     foreach ($order->items as $item){
    //         $product = $item->product;
    //         //计算对应的商品销量
    //         $soldCount = OrderItem::query()
    //         ->where('product_id',$product->id)
    //         ->whereHas('order',function($query){
    //             $query->whereNotNull('paid_at');
    //         })->sum('amount');     
    //         //更新商品销量
    //         $product->update([
    //             'sold_count' => $soldCount
    //         ]);
    //     }
    // }
}
