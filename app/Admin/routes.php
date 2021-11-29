<?php

use Illuminate\Routing\Router;


Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    //$router->get('users','UsersController@index');
   
    $router->resource('users',UsersController::class);
    $router->resource('categories',CategoryController::class);
    $router->resource('products',ProductsController::class);
    $router->resource('topcategories',TopCategoriesController::class);
    $router->resource('productskus',ProductSkusController::class);
    $router->resource('productinfos',ProductInfosController::class);
    $router->get('orders','OrdersController@index')->name('orders.index');

    $router->get('/orders/{order}','OrdersController@show')->name('orders.show');

    $router->post('orders/{order}/ship','OrdersController@ship')->name('orders.ship');

});
