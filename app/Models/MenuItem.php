<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['name', 'price', 'qty', 'category', 'availability', 'product_id'];

    // Relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
