<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['name', 'price', 'qty', 'category_id', 'availability'];

    // Relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderDetail::class);
    }
}
