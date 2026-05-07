<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Followers extends Model
{
    protected $fillable = [
        'follower_id',
        'following_id'
    ];

    public function followerUser(){
        return $this->belongsToMany(User::class, 'follower_id');
    }
    public function followingUser(){
        return $this->belongsToMany(User::class,'following_id');
    }
}
