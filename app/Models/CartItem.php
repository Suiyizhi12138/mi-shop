<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['amount'];

    //建立与用户一对多关系
    public function user(){
        return $this->belongsTo(User::class);
    }

    //建立与商品sku 一对多关系
    public function productSku(){
        return $this->belongsTo(ProductSku::class);
    }
    
}
