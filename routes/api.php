<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/categories',[CategoriesController::class,'getCategories']);

Route::get('/alltopcategories',[CategoriesController::class,'getAllTopCategories']);
Route::get('/allcategories',[CategoriesController::class,'getAllCategories']);
Route::get('/product/{id}',[ProductsController::class,'getProduct']);



//
Route::post('login', [UserController::class,'login']);
Route::post('register', [UserController::class,'register']);
Route::post('refreshtoken', [UserController::class,'refreshtoken']);

Route::post('/search-products',[ProductsController::class,'searchProducts']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', [UserController::class,'logout']);
    Route::get('userinfo', [UserController::class,'userinfo']);
    //添加到购物车
    Route::post('add',[CartController::class,'add']);
    //获取个人信息
    Route::get('personalinfo',[UserController::class,'getPersonalInfo']);
    //更改个人信息
    Route::post('personalinfo',[UserController::class,'updatePersonalInfo']);

    Route::post('saveavatar',[UserController::class,'saveAvatar']);
    //获取购物车信息
    Route::get('get_cart_items',[CartController::class,'getCartItems']);
    //删除单个购物车项目
    Route::delete('delete_cart_item',[CartController::class,'deleteCartItem']);
    //获取用户地址
    Route::get('user_addresses',[UserAddressController::class,'getAddresses']);
    //删除单个地址
    Route::delete('user_address',[UserAddressController::class,'deleteAddress']);
    //添加新地址
    Route::post('user_address',[UserAddressController::class,'addAddress']);
    //更新地址
    Route::post('update_address',[UserAddressController::class,'updateAddress']);
    //创建订单
    Route::post('/add_order',[OrderController::class,'addOrder']);
    //查询订单
    Route::get('/order/{no}',[OrderController::class,'getOrder']);
    //查询全部订单
    Route::get('/orders',[OrderController::class,'getOrders']);
    //跳转支付宝支付页面
    //Route::get('/payment/alipay',[PaymentController::class,'payByAlipay']);

    //获取支付宝回调前端数据
    //Route::get('/payment/alipay/return',[PaymentController::class,'alipayReturn']);
    
    //添加喜欢路由
    Route::post('/product/{product}/like',[ProductsController::class,'addToLike']);

    //取消喜欢商品
    Route::delete('/product/{product}/unlike',[ProductsController::class,'deleteLike']);

    //查询喜欢的商品
    Route::get('/product/like/products',[ProductsController::class,'getLikeProducts']);

});