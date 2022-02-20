<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserAddress;
use Illuminate\Support\Carbon;
use App\Models\ProductSku;
use App\Models\Product;
use App\Jobs\CloseOrder;
use App\Exceptions\InvalidRequestException;

class OrderController extends Controller
{
    //创建订单
    public function addOrder(Request $request)
    {
        $user = $request->user();
        //开启一个数据库事务
        $order = DB::transaction(function () use ($user, $request) {
            $address = UserAddress::find($request->input('address_id'));
            
            //更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);

            //创建一个订单
            $order = new Order([
                'address' => [
                    //将地址信息放入订单中
                    'address' => $address->full_address,
                    'contact_name' => $address->contact_name,
                    'contact_phone'=>$address->contact_phone

                ],
                
                'total_amount'=>0
            ]);
            //订单关联到当前用户
            $order->user()->associate($user);
            //写入数据库
            $order->save();

            $totalAmount = 0;
            $items = $request->input('items');

            //遍历用户提交的 SKU
            foreach($items as $item){
                $sku = ProductSku::find($item['product_sku_id']);
                $product = Product::find($sku->product_id);
                
                //创建一个OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount'=>$item['amount'],
                    'price'=>$sku->price
                    
                ]);

                $item->product()->associate($product);
                $item->productSku()->associate($sku);
                

                $item->save();
                
                $totalAmount += $sku->price*$item['amount'];
                //减库存
                if($sku->decreaseStock($item['amount'])<=0){
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            //更新订单总金额
            $order->update(['total_amount'=>$totalAmount]);
            $order->save();
            //将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('product_sku_id');
            $user->cartItem()->whereIn('product_sku_id',$skuIds)->delete();
            return $order;
        });
        //触发关闭订单任务
        $this->dispatch(new CloseOrder($order,config('app.order_ttl')));
        
        return $this->getOrder($order->no);
    }
    //修改订单
    public function updateOrder()
    {
    }
    //删除订单
    public function deleteOrder(Request $request){
        $no = $request->input('no');
        Order::where('no','=',$no)->delete();
        return response()->json(["message"=>"success"]);
    }
    //查询当前用户所有订单
    public function getOrders(Request $request)
    {   
        $user = $request->user();
        $userId = $user->id;
        $orders = Order::where('user_id','=',$userId)->with([
            'items'=>function($query){
                $query->with('product');
                $query->with('productSku');
                $query->orderBy('id');
            }
        ])->get();
        return response()->json($orders);
    }
    //查询当前订单
    public function getOrder($no){
        
        $order = Order::where('no','=',$no)
        ->with([
            'items'=>function($query){
                $query->with('product');
                $query->with('productSku');
            }
        ])->get();
       
        return response()->json($order);
    }

    //确认收货
    public function received(Order $order, Request $request){
        //确认订单 权限

        if($order->ship_status !== Order::SHIP_STATUS_DELIVERED){
            throw new InvalidRequestException('发货状态不正确');
        }

        //更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        //返回原页面
        return redirect()->back();
       

    }
}
