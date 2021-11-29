<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;



class CartController extends Controller
{
    //添加到购物车
    public function add(AddCartRequest $request){
        $user = $request->user();
        $sku_id = $request->input('sku_id');
        $amount = $request->input('amount');
        $sku = ProductSku::where('id',$sku_id)->first();

        //从数据库中查找该商品是否已经在购物车中
        if($cart=$user->cartItem()->where('product_sku_id',$sku_id)->first()){
            
         
            //判断添加后是否超过限购数量
            if($cart->amount+$amount>$sku->limit){
               throw new InvalidRequestException('订单数量超过限制');
            }
            //添加数量
            $cart->update(['amount'=>$cart->amount+$amount]);
        }//否则创造一个新的购物车记录
        else{
            $cart = new CartItem(['amount'=>$amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($sku_id);
            $cart->save();
        }

    }

    //查询购物车
    public function getCartItems(Request $request){
        $user = $request->user();
        $cartItems = CartItem::where('user_id','=',$user->id)->with([
            'productSku'=>function($query){
                $query->with('product');
            }
        
        ])->get();
        return response()->json($cartItems);
    }
    //删除购物车项目
    public function deleteCartItem(Request $request){
        $user = $request->user();
        $itemId = $request->input('item_id');

        
        $cartItem = CartItem::where('id','=',$itemId);
        $cartItem->delete();
        $cartItems = CartItem::where('user_id','=',$user->id)->get();
        return response()->json($cartItems);
    }
}
