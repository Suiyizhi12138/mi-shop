<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,DefaultDatetimeFormat,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    //与购物车项目 一对多关联
    public function cartItem(){
        return $this->hasMany(CartItem::class);
    }

    //与用户信息 一对一关联
    public function personalInfo(){
        return $this->hasOne(UserInfo::class);
    }

    //与用户地址一对多关联
    public function user_address(){
        return $this->hasMany(UserAddress::class);
    }

    //与订单项目一对多关联
    public function orderItem(){
        return $this->hasMany(Order::class);
    }
    //收藏商品
    public function likedProducts(){
        return $this->belongsToMany(Product::class,'user_like_products')
        ->withTimeStamps()
        ->orderBy('user_like_products.created_at','desc');
    }
    //与用户虚拟账户一对一关联
    public function virtualAccount(){
        return $this->hasOne(UserVirtualAccount::class);
    }
}
