<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
  protected $fillable = [
    'session_id',
    'product_id',
    'quantity',
    'price',
    'size_id'
  ];

  protected function product()
  {
    return $this->belongsTo(Product::class);
  }
}
