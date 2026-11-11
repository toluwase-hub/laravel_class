<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'name',
        'description',
        'category',
        'price',
        'image',
    ];

    public function size()
    {
        return $this->hasMany(Size::class);
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
