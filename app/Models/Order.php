<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Define the table name (optional if the table name is 'orders')
    protected $table = 'orders';

    // Specify the fillable attributes for mass assignment
    protected $fillable = [
        'order_number',
        'customer_name',
        'items',
        'total_price',
        'status',
    ];
    

    // Cast attributes to appropriate types
    protected $casts = [
        'items' => 'array', // Automatically casts the items to and from JSON
    ];
}
