<?php

namespace App\Providers;
use Illuminate\Support\Facades\Schema;
//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Monolog\Logger;
use Yansongda\Pay\Pay;


class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        //往服务器中注入一个名为 Alipay 的单例对象

        $this->app->singleton('alipay',function(){
            $config = config('pay.alipay');
            $config['notify_url'] = ngrok_url('payment.alipay.notify');
            $config['return_url'] = route('payment.alipay.return');
            //判断当前项目运行环境是否为线上环境
            if(app()->environment() !== 'production'){
                $config['mode'] = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::WARNING;
            }

            //调用 Yansongda\Pay 来创建一个支付宝支付对象
             return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay',function(){
            $config = config('pay.wechat');
            if(app()->environment() !== 'production'){
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::WARNING;
            }

            //调用Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

        $this->registerPolicies();
        Passport::routes();
    }
}
