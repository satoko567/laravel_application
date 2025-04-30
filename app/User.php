<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function latestStore()
    {
        return $this->hasOne(Store::class)->latest('created_at');
    }



    public function likes()
    {
        return $this->belongsToMany(Store::class, 'likes', 'user_id', 'store_id')->withTimestamps();
    }


    public function like($storeId)
    {
        $exist = $this->isLike($storeId);
        if ($exist) {
            return false;
        } else {
            $this->likes()->attach($storeId);
            return true;
        }
    }


    public function unlike($storeId)
    {
        $exist = $this->isLike($storeId);
        if ($exist) {
            $this->likes()->detach($storeId);
            return true;
        } else {
            return false;
        }
    }

    public function isLike($storeId)
    {
        return $this->likes()->where('store_id', $storeId)->exists();
    }

}
