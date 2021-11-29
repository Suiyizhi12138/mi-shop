<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;
class Category extends Model
{
    use HasFactory,DefaultDatetimeFormat;
    protected $fillable = ['name'];
    //与商品关联
    public function products(){
    return $this->hasMany(Product::class);
    
    }
}
