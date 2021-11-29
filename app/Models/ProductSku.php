<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use App\Exceptions\InternalException;
class ProductSku extends Model
{
    use HasFactory,DefaultDatetimeFormat;
    protected $fillable = ['name','description','price','stock','imgs','limit'];
    protected $casts = [
        "imgs"=>"json"
    ];
    //与商品模型关联
    public function product(){
        return $this->belongsTo(Product::class);
    }
    //与购物车item模型 一对多关联
    public function cartItem(){
        return $this->hasMany(CartItem::class);
    }
    //与订单模型一对多关联
    public function orderItem(){
        return $this->hasMany(OrderItem::class);
    }
    //数据库减库存
    public function decreaseStock($amount){
        if($amount<0){
           throw new InternalException('减库存不能小于0');
        }

        return $this->where('id',$this->id)->where('stock','>=',$amount)->decrement('stock',$amount);
    }
    //加库存
    public function addStock($amount){
        if($amount<0){
            throw new InternalException('加库存不能小于0');
        }

        $this->increment('stock',$amount);
    }
    
}
