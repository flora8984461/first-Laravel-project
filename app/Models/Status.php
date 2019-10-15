<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content']; //在微博模型的fillable属性中允许更新微博的 content 字段
    public function user()
    {
        return $this->belongsTo(User::class); //一条微博属于一个用户
    }
}
