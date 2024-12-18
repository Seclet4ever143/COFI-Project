<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'payment_method',
        'gcash_no',
        'total_amount',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
