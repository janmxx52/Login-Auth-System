<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'order_status',
        'products',
        'reason',
        'status',
    ];

    protected $casts = [
        'products' => 'array',
    ];
}
