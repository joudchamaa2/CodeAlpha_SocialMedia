<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasApiTokens ;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function posts(){
        return $this->hasMany(Posts::class);
    }
    public function likes(){
        return $this->hasMany(Likes::class);
    }
    public function comments(){
        return $this->hasMany(Comments::class);
    }
    //people I follow
    public function following(){
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')->withPivot('status')->withTimestamps();
    }
    //people who follow me
    public function followers(){
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id')->withPivot('status')->withTimestamps();
    }
}
