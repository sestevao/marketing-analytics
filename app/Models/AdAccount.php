<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdAccount extends Model
{
    /** @use HasFactory<\Database\Factories\AdAccountFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'platform',
        'account_id',
        'access_token',
        'refresh_token',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
