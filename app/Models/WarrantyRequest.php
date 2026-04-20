<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'processed_by',
        'processed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'products' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
