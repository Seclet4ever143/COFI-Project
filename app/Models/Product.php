<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'category', 'price', 'qty'];

    // Relationship to MenuItem
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
