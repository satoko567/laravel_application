<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;
    
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
        'category',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
