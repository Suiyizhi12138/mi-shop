<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('index');
})
->name('app.welcome');
//支付宝支付测试界面
// Route::get('alipay',function(){
//     return app('alipay')->web([
//         'out_trade_no' => time(),
//         'total_amount' => 1,
//         'subject' => 'test subject - 测试'
//     ]);
// });

Route::get('/payment/alipay/return',[PaymentController::class,'alipayReturn'])
->name('payment.alipay.return');

//跳转支付宝支付路由
Route::get('/payment/{order}/alipay', [PaymentController::class, 'payByAlipay']);


//支付宝回调


//服务器回调
Route::post('/payment/alipay/notify',[PaymentController::class,'alipayNotify'])
->name('payment.alipay.notify');

//测试路由
// Route::get('/update/sold/count/{no}',[PaymentController::class,'updateSoldCount']);

//练习路由 test route
Route::get("test",function(){
    return view('test');
});