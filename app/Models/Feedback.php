<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Feedback extends Model
{
    protected $table = 'feedbacks'; // tambahkan ini

    protected $fillable = [
        'session_token',     'has_receipt', 'language', 'receipt_number', 'total_purchase', 'country',
        'overall_rating', 'staff_friendliness', 'staff_helpfulness', 'checkout_process',
        'service_speed', 'store_cleanliness', 'store_ambience', 'product_display',
        'product_variety', 'product_quality', 'product_design', 'price_appropriateness',
        'value_for_money', 'like_most', 'improvement_suggestion', 'recommend_likelihood',
        'may_contact', 'full_name', 'email', 'whatsapp',
        'current_step', 'total_points', 'completed_at', 'ip_address',
    ];

    protected $casts = [
        'has_receipt' => 'boolean',

        'may_contact' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // Bobot poin per page — dipakai saat pindah step
    public const POINT_WEIGHTS = [
        2 => 10,
        3 => 20,
        4 => 20,
        5 => 20,
        6 => 15,
        7 => 5,
    ];

    public function spin(): HasOne
    {
        return $this->hasOne(FeedbackSpin::class);
    }

    /**
     * current_step tidak bisa dipakai di sini: tombol kirim melompat ke langkah 7, dan langkah 7 menjadikannya 8.
     */
    public function hasAnsweredThroughStepSix(): bool
    {
        $requiredAnswers = [
            'staff_friendliness', 'staff_helpfulness', 'checkout_process', 'service_speed',
            'store_cleanliness', 'store_ambience', 'product_display', 'product_variety',
            'product_quality', 'product_design', 'price_appropriateness', 'value_for_money',
            'recommend_likelihood',
        ];

        foreach ($requiredAnswers as $answer) {
            if ($this->getAttribute($answer) === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Panggil ini tiap kali guest submit satu page, sebelum simpan.
     */
    public function advanceStep(int $completedStep): void
    {
        if ($completedStep >= $this->current_step) {
            $this->total_points += self::POINT_WEIGHTS[$completedStep] ?? 0;
            $this->current_step = $completedStep + 1;
        }
    }
}
