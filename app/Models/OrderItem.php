<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount','price','rating','review','reviewed_at'
    ];

    protected $dates = ['reviewed_at'];

    public $timestamps = false;
    //多对一 所属商品
    public function product(){
        return $this->belongsTo(Product::class);
    }
    //多对一 所属sku
    public function productSku(){
        return $this->belongsTo(ProductSku::class);
    }
    //多对一 所属订单
    public function order(){
        return $this->belongsTo(Order::class);
    }
    


}
