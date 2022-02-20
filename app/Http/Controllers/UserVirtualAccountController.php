<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\UserVirtualAccount;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;

class UserVirtualAccountController extends Controller
{
    //查询账户
    public function getAccount(Request $request){
        $user = $request->user();
        $account = UserVirtualAccount::where('user_id','=',$user->id)->first();
        return response()->json($account);
    }
    //消费
    public function cost(Request $request){
        $user  = $request->user();
        $account = UserVirtualAccount::where('user_id','=',$user->id)->first();
        $cost = $request->input('cost');
        $orderNo = $request->input('no');
        $order = Order::where('no','=',$orderNo)->first();
        //如果用户没有虚拟账户 则创建一个
        if(!$account){
            $account = new UserVirtualAccount([
                'balance' => 1000000 - $cost
            ]);
            $account -> user() -> associate($user);
            $account -> save();
        }else{
            //如果订单已经支付过 则不能支付
            if($order->paid_at){
                throw new InvalidRequestException('订单已经支付');
            }
            //如果余额不足 则不能支付
            if($account->balance < $cost){
                throw new InvalidRequestException('余额不足');
            }
            $order->update([
                'paid_at'=> Carbon::now()
            ]);
            $account->update([
                'balance' => $account->balance - $request->input('cost')
            ]);
            $account->save();
        }
        return response()->json($account);
    }
}
