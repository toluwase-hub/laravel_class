<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Size extends Model
{
   protected $fillable = [
     'product_id',
     'size',
     'in_stock',
     'out_of_stock'
    ];

    public function product()
    {
     return $this->belongsTo(Product::class);
    }
}
