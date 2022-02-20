<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVirtualAccount extends Model
{
    use HasFactory;
    //允许批量赋值字段
    protected $fillable = [
        'balance'
    ];
    //与用户一对一关联
    public function user(){
        return $this->belongsTo(User::class);
    }
}
