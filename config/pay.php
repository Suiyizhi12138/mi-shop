<?php
 return [
   'alipay' => [
     'app_id' => env('ALIPAY_APP_ID'),
     'ali_public_key'  => env('ALIPAY_PUBLIC_KEY'),
     'private_key' => env('ALIPAY_PRIVATE_KEY'),
     'log' => [
       'file' => storage_path('logs/alipay.log')
     ]
     ],

   'wechat' => [
     'app_id' => '',
     'mch_id' => '',
     'key' => '',
     'cerrt_client' => '',
     'log' => [
       'file' => storage_path('logs/wechat_pay.log')
     ]
   ]
 ];
