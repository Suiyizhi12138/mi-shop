<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class Product extends Model
{
    use HasFactory,DefaultDatetimeFormat;
    protected $fillable = ['name','description','image_url','price','on_sale','sold_count'];

    //与分类模型关联
    public function category(){
        return $this->belongsTo(Category::class);
    }
    
    //与商品sku关联
    public function skus(){
        return $this->hasMany(ProductSku::class);
    }

    
    //与商品概述图片关联
    public function details(){
        return $this->hasMany(ProductDetail::class);
    }

    //与商品参数图片关联

    public function parameters(){
        return $this->hasMany(ProductParameter::class);
    }

    //与商品信息关联
    public function productinfos(){
        return $this->hasOne(ProductInfo::class);
    }
    //与订单项目 一对多关联
    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }
    //
}
