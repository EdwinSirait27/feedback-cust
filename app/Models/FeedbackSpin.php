<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class FeedbackSpin extends Model
{
    protected $fillable = [
        'feedback_id', 'prize_type', 'voucher_code',
        'voucher_amount', 'is_redeemed', 'redeemed_at', 'spun_at',
    ];

    protected $casts = [
        'is_redeemed' => 'boolean',
        'redeemed_at' => 'datetime',
        'spun_at' => 'datetime',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }
}