<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'current_users',
        'action_type',
        'table_name',
        'record_id',
        'old_data',
        'new_data',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
