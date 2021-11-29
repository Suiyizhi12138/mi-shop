<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;
    
    protected $fillable = ['avatar','nick_name','sex','country']; 

    //与用户主表一对一关联
    public function user(){
        return $this->belongsTo(User::class);
    }
}
