<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'district',
        'address',
        'contact_name',
        'contact_phone',
        'last_used_at',
        'tag'
    ];

    //表示 last_used_at 是一个时间日期类型
    protected $dates = ['last_used_at'];

    //归属用户
    public function user(){
        return $this->belongsTo(User::class);
    }
    //创建一个访问器获取完整地址 通过 $address->full_address 来访问
    public function getFullAddressAttribute(){
        return "{$this->district}_{$this->address}";
    }
}
