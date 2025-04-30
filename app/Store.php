<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Like;

class Store extends Model
{
    
    protected $fillable = [
        'name',
        'google_map_url',
        'place_id',
        'address',
        'latitude',
        'longitude',
        'phone_number',
        'website',
        'image',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'store_id', 'user_id')->withTimestamps();
    }


    public function likes()
    {
        return $this->hasMany(Like::class, 'store_id', 'id');
    }
    

    public function getLatestLikeAttribute()
    {
        return $this->likes()->orderBy('created_at', 'desc')->first();
    }
}
