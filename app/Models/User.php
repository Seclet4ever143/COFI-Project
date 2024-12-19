<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'role_id', 'name', 'email', 'phone', 'password', 'google_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }
    
    public function products()
    {
        return $this->hasMany(Product::class, 'updated_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
