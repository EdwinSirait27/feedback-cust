<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

// class DailyPrizeQuota extends Model
// {
//     protected $table = 'daily_prize_quotas';

//     protected $fillable = [
//         'date',
//         'voucher_100k_used',
//         'voucher_50k_used',
//         'voucher_25k_used',
//     ];

//     protected $casts = [
//         'date' => 'date',
//     ];

//     const QUOTA = [
//         'voucher_100k' => 2,
//         'voucher_50k'  => 2,
//         'voucher_25k'  => 4,
//     ];

//     public static function today(): self
//     {
//         return self::firstOrCreate(
//             ['date' => Carbon::today()->toDateString()],
//             [
//                 'voucher_100k_used' => 0,
//                 'voucher_50k_used'  => 0,
//                 'voucher_25k_used'  => 0,
//             ]
//         );
//     }

//     public function remaining(string $type): int
//     {
//         $used = match ($type) {
//             'voucher_100k' => $this->voucher_100k_used,
//             'voucher_50k'  => $this->voucher_50k_used,
//             'voucher_25k'  => $this->voucher_25k_used,
//             default        => 999,
//         };

//         return max(0, self::QUOTA[$type] - $used);
//     }

//     public function increment(string $type): void
//     {
//         match ($type) {
//             'voucher_100k' => $this->increment('voucher_100k_used'),
//             'voucher_50k'  => $this->increment('voucher_50k_used'),
//             'voucher_25k'  => $this->increment('voucher_25k_used'),
//             default        => null,
//         };
//     }
// }
class DailyPrizeQuota extends Model
{
    protected $table = 'daily_prize_quotas';

    protected $fillable = [
        'date',
        'voucher_100k_used',
        'voucher_50k_used',
        'voucher_25k_used',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    const QUOTA = [
        'voucher_100k' => 2,
        'voucher_50k'  => 2,
        'voucher_25k'  => 4,
    ];

    public static function today(): self
    {
        return self::firstOrCreate(
            ['date' => Carbon::today()->toDateString()],
            [
                'voucher_100k_used' => 0,
                'voucher_50k_used'  => 0,
                'voucher_25k_used'  => 0,
            ]
        );
    }

    public function remaining(string $type): int
    {
        $used = match ($type) {
            'voucher_100k' => $this->voucher_100k_used,
            'voucher_50k'  => $this->voucher_50k_used,
            'voucher_25k'  => $this->voucher_25k_used,
            default        => 999,
        };

        return max(0, self::QUOTA[$type] - $used);
    }

    // Ganti nama dari increment() ke addUsed()
    public function addUsed(string $type): void
    {
        match ($type) {
            'voucher_100k' => $this->increment('voucher_100k_used'),
            'voucher_50k'  => $this->increment('voucher_50k_used'),
            'voucher_25k'  => $this->increment('voucher_25k_used'),
            default        => null,
        };
    }
}