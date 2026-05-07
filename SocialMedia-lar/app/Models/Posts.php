<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function images(){
        return $this->hasMany(PostImage::class);
    }
    public function likes(){
        return $this->hasMany(Likes::class);
    }
    public function comments(){
        return $this->hasMany(Comments::class);
    }
}
