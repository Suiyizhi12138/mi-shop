<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInfo extends Model
{
    use HasFactory;
    protected $fillable = ['product_info'];
    protected $casts = [
        'product_info' => 'json'
    ];
    
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
