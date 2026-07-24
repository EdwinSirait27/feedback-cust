<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackSpin;
use App\Models\VoucherBelanja;
use App\Models\DailyPrizeQuota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\JualHead;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    private const TOTAL_STEPS = 7;
    private const MIN_REQUIRED_STEP = 2;
    private function getCountries(): array
    {
        $raw = require base_path('vendor/umpirsky/country-list/data/en/country.php');

        return collect($raw)
            ->map(fn($name, $code) => ['code' => $code, 'name' => $name])
            ->sortBy('name')
            ->values()
            ->all();
    }

    public function welcome(): Response
    {
        $countries = require base_path('vendor/umpirsky/country-list/data/en/country.php');

        return Inertia::render('Feedback/Welcome', [
            'countries' => collect($countries)
                ->map(fn($name, $code) => ['code' => $code, 'name' => $name])
                ->sortBy('name')
                ->values(),
        ]);
    }
    public function selectLanguage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'language' => ['required', 'string', 'in:id,en,zh,ja,ko,ru'],
        ]);

        $feedback = $this->currentFeedback() ?? $this->createFeedback($request);
        $feedback->update(['language' => $data['language'], 'current_step' => 2]);

        $request->session()->put('feedback_id', $feedback->id);

        return redirect()->route('feedback.step', ['step' => 2]);
    }
    public function showStep(int $step): Response|RedirectResponse
    {
        Log::info('showStep', ['step' => $step, 'component' => "Feedback/Page{$step}"]);
        $feedback = $this->currentFeedback();

        if (! $feedback) {
            return redirect()->route('home');
        }

        // Step 7 selalu boleh diakses langsung (finish_now dari step manapun)
        if ($step !== 7 && $step > $feedback->current_step) {
            return redirect()->route('feedback.step', ['step' => $feedback->current_step]);
        }

        $props = ['feedback' => $feedback];

        if ($step === 2) {
            $props['countries'] = $this->getCountries();
        }

        return Inertia::render("Feedback/Page{$step}", $props);
    }

    public function updateStep(Request $request, int $step): RedirectResponse
    {
        // dd($request->all(), $request->boolean('finish_now'));
        $feedback = $this->currentFeedback();

        if (! $feedback) {
            return redirect()->route('home');
        }

        // $validated = $request->validate($this->rulesForStep($step, $feedback));
        $validated = $request->validate($this->rulesForStep($step, $feedback, $request));

        if ($step === 2 && ! empty($validated['receipt_number'])) {
            $jual = JualHead::where('NO_EJ', $validated['receipt_number'])
                ->whereBetween('TGL', [
                    Carbon::now()->startOfMonth()->toDateString(),
                    Carbon::now()->endOfMonth()->toDateString(),
                ])
                ->first();

            if (! $jual) {
                $jualExpired = JualHead::where('NO_EJ', $validated['receipt_number'])->exists();
                throw ValidationException::withMessages([
                    'receipt_number' => $jualExpired
                        ? __('validation.receipt_expired', ['month' => Carbon::now()->translatedFormat('F Y')])
                        : __('validation.receipt_not_found'),
                ]);
            }

            $alreadyUsed = Feedback::where('receipt_number', $validated['receipt_number'])
                ->where('id', '!=', $feedback->id)
                ->whereNotNull('completed_at')
                ->exists();

            if ($alreadyUsed) {
                throw ValidationException::withMessages([
                    'receipt_number' => __('validation.receipt_used'),
                ]);
            }

            $validated['total_purchase'] = $jual->NETTO;
        }

        $feedback->fill($validated);
        $feedback->advanceStep($step);
        $feedback->save();

        $wantsToFinishNow = $request->boolean('finish_now');

        if ($wantsToFinishNow) {
            // dd('masuk finish_now', $step); // tambah ini
            if ($step < 7) {
                return redirect()->route('feedback.step', ['step' => 7]);
            }
            return redirect()->route('feedback.spin');
        }

        // dd('tidak masuk finish_now', $wantsToFinishNow); // dan ini

        if ($step >= self::TOTAL_STEPS) {
            return redirect()->route('feedback.spin');
        }

        return redirect()->route('feedback.step', ['step' => $step + 1]);
    }

    private function rulesForStep(int $step, Feedback $feedback, Request $request): array
    {
        return match ($step) {
            2 => [
                'has_receipt'   => ['required', 'boolean'],
                'receipt_number' => [
                    Rule::requiredIf(fn() => $request->boolean('has_receipt')),
                    'nullable',
                    'string',
                ],
                'total_purchase' => [
                    Rule::requiredIf(fn() => $request->boolean('has_receipt')),
                    'nullable',
                    'integer',
                    'min:0',
                    'max:13',
                ],
                'country'        => ['required', 'string'],
                'overall_rating' => ['required', 'integer', 'between:1,5'],
            ],
            3 => [
                'staff_friendliness' => ['required', 'integer', 'between:1,5'],
                'staff_helpfulness'  => ['required', 'integer', 'between:1,5'],
                'checkout_process'   => ['required', 'integer', 'between:1,5'],
                'service_speed'      => ['required', 'integer', 'between:1,5'],
            ],
            4 => [
                'store_cleanliness' => ['required', 'integer', 'between:1,5'],
                'store_ambience'    => ['required', 'integer', 'between:1,5'],
                'product_display'   => ['required', 'integer', 'between:1,5'],
                'product_variety'   => ['required', 'integer', 'between:1,5'],
            ],
            5 => [
                'product_quality'       => ['required', 'integer', 'between:1,5'],
                'product_design'        => ['required', 'integer', 'between:1,5'],
                'price_appropriateness' => ['required', 'integer', 'between:1,5'],
                'value_for_money'       => ['required', 'integer', 'between:1,5'],
            ],
            6 => [
                'like_most'              => ['nullable', 'string', 'max:1000'],
                'improvement_suggestion' => ['nullable', 'string', 'max:1000'],
                'recommend_likelihood'   => ['required', 'integer', 'between:0,10'],
            ],
            7 => [
                'may_contact' => ['required', 'boolean'],
                'full_name'   => ['nullable', 'string', 'max:255', 'required_if:may_contact,true'],
                'email'       => ['nullable', 'email', 'required_if:may_contact,true'],
                'whatsapp'    => ['nullable', 'string', 'max:20'],
            ],
            default => [],
        };
    }


    public function spinPage(): Response|RedirectResponse
    {
        $feedback = $this->currentFeedback();

        if (! $feedback || $feedback->current_step <= self::MIN_REQUIRED_STEP) {
            return redirect()->route('home'); // sebelumnya 'feedback.welcome'
        }

        return Inertia::render('Feedback/Spin', [
            'alreadySpun' => $feedback->spin()->exists(),
            'prize' => $feedback->spin,
        ]);
    }


    // public function spin(): RedirectResponse
    // {
    //     $feedback = $this->currentFeedback();

    //     abort_if(! $feedback || $feedback->current_step <= self::MIN_REQUIRED_STEP, 403, 'Data wajib belum lengkap.');
    //     abort_if($feedback->spin()->exists(), 409, 'Sudah pernah spin.');

    //     DB::transaction(function () use ($feedback) {
    //         $prize = $this->rollPrize($feedback->current_step);

    //         $voucherCode = $prize['amount'] ? $this->generateVoucherCode() : null;

    //         $spin = $feedback->spin()->create([
    //             'prize_type'     => $prize['type'],
    //             'voucher_code'   => $voucherCode,
    //             'voucher_amount' => $prize['amount'],
    //             'spun_at'        => now(),
    //         ]);

    //         // Simpan ke voucher_belanja di database kasir kalau menang
    //         if ($prize['amount'] && $voucherCode) {
    //             VoucherBelanja::create([
    //                 'NO_VCHR'       => $voucherCode,
    //                 'ID'            => 'System',
    //                 'TGL'           => Carbon::today()->toDateString(),
    //                 'EXPIRED'       => Carbon::today()->addDay()->toDateString(),
    //                 'MEMBER'        => '-',
    //                 'KET'           => 'Voucher Feedback ' . number_format($prize['amount'], 0, ',', '.'),
    //                 'NILAI_VOUCHER' => $prize['amount'],
    //                 'TARIK'         => 0,
    //                 'NO_EJB'        => 0,
    //                 'TGLTRX'        => null,
    //                 'BO'            => 'B01',
    //                 'NO_EJ'         => '-',
    //             ]);
    //         }

    //         if ($prize['type'] !== 'no_prize') {
    //             $quota = DailyPrizeQuota::today();
    //             $quota->addUsed($prize['type']);
    //         }


    //         $feedback->update(['completed_at' => now()]);

    //         // Kirim email kalau menang dan ada email
    //         if ($prize['amount'] && $feedback->email) {
    //             Mail::to($feedback->email)->queue(new \App\Mail\VoucherMail($feedback, $spin));
    //         }
    //     });

    //     return redirect()->route('feedback.spin');
    // }
    public function spin(): RedirectResponse
{
    $feedback = $this->currentFeedback();

    abort_if(! $feedback || $feedback->current_step <= self::MIN_REQUIRED_STEP, 403, 'Data wajib belum lengkap.');
    abort_if($feedback->spin()->exists(), 409, 'Sudah pernah spin.');

         // Double-check sebelum masuk transaction
    if ($feedback->spin()->exists()) {
        return redirect()->route('feedback.spin');
    }

    DB::transaction(function () use ($feedback) {
        // Lock row quota hari ini supaya tidak ada race condition
        $quota = DailyPrizeQuota::lockForUpdate()->firstOrCreate(
            ['date' => Carbon::today()->toDateString()],
            ['voucher_100k_used' => 0, 'voucher_50k_used' => 0, 'voucher_25k_used' => 0]
        );

        $prize = $this->rollPrize($feedback->current_step);

        // Validasi ulang quota di dalam lock — ini yang cegah race condition
        if ($prize['type'] !== 'no_prize') {
            if ($quota->remaining($prize['type']) <= 0) {
                // Quota habis saat lock — fallback ke no_prize
                $prize = ['type' => 'no_prize', 'amount' => null];
            }
        }

        $voucherCode = $prize['amount'] ? $this->generateVoucherCode() : null;

        $spin = $feedback->spin()->create([
            'prize_type'     => $prize['type'],
            'voucher_code'   => $voucherCode,
            'voucher_amount' => $prize['amount'],
            'spun_at'        => now(),
        ]);

        if ($prize['amount'] && $voucherCode) {
            VoucherBelanja::create([
                'NO_VCHR'       => $voucherCode,
                'ID'            => 'System',
                'TGL'           => Carbon::today()->toDateString(),
                'EXPIRED'       => Carbon::today()->addDays(30)->toDateString(),
                'MEMBER'        => $feedback->full_name ?? '-',
                'KET'           => 'Voucher Feedback ' . number_format($prize['amount'], 0, ',', '.'),
                'NILAI_VOUCHER' => $prize['amount'],
                'TARIK'         => 0,
                'NO_EJB'        => 0,
                'TGLTRX'        => null,
                'BO'            => 'B01',
                'NO_EJ'         => $feedback->receipt_number ?? '-',
            ]);
        }

        if ($prize['type'] !== 'no_prize') {
            $quota->addUsed($prize['type']);
        }

        $feedback->update(['completed_at' => now()]);

        if ($prize['amount'] && $feedback->email) {
            Mail::to($feedback->email)->queue(new \App\Mail\VoucherMail($feedback, $spin));
        }
    });

    return redirect()->route('feedback.spin');
}

    public function result(): Response|RedirectResponse
    {
        $feedback = $this->currentFeedback();

        if (! $feedback || ! $feedback->spin) {
            return redirect()->route('home'); // sebelumnya 'feedback.welcome'
        }

        return Inertia::render('Feedback/Result', [
            'spin' => $feedback->spin,
        ]);
    }

    private function currentFeedback(): ?Feedback
    {
        $id = request()->session()->get('feedback_id');

        return $id ? Feedback::find($id) : null;
    }

    private function createFeedback(Request $request): Feedback
    {
        return Feedback::create([
            'session_token' => (string) Str::uuid(),
            'ip_address' => $request->ip(),
        ]);
    }




    // private function rollPrize(int $currentStep): array
    // {
    //     $quota = DailyPrizeQuota::today();

    //     // Hitung total feedback yang sudah completed hari ini
    //     $todayFeedbackCount = Feedback::whereNotNull('completed_at')
    //         ->whereDate('completed_at', Carbon::today())
    //         ->count();

    //     // Tentukan prize pool yang eligible
    //     $eligiblePrizes = [];

    //     if ($currentStep >= 6) {
    //         // Step 6+ eligible semua prize
    //         // 100k hanya eligible kalau sudah ada 100 feedback hari ini
    //         if ($todayFeedbackCount >= 100 && $quota->remaining('voucher_100k') > 0) {
    //             $eligiblePrizes[] = ['type' => 'voucher_100k', 'amount' => 100000, 'weight' => 5];
    //         }
    //         if ($quota->remaining('voucher_50k') > 0) {
    //             $eligiblePrizes[] = ['type' => 'voucher_50k', 'amount' => 50000, 'weight' => 15];
    //         }
    //         if ($quota->remaining('voucher_25k') > 0) {
    //             $eligiblePrizes[] = ['type' => 'voucher_25k', 'amount' => 25000, 'weight' => 30];
    //         }
    //     } else {
    //         // Step 2–5 hanya eligible 25k dan 50k
    //         if ($quota->remaining('voucher_50k') > 0) {
    //             $eligiblePrizes[] = ['type' => 'voucher_50k', 'amount' => 50000, 'weight' => 15];
    //         }
    //         if ($quota->remaining('voucher_25k') > 0) {
    //             $eligiblePrizes[] = ['type' => 'voucher_25k', 'amount' => 25000, 'weight' => 30];
    //         }
    //     }
    //     // Roll dengan weighted random
    //     $roll = random_int(1, 100);
    //     $totalWeight = array_sum(array_column($eligiblePrizes, 'weight'));

    //     if (! empty($eligiblePrizes) && $roll <= $totalWeight) {
    //         // Weighted pick
    //         $rand = random_int(1, $totalWeight);
    //         $cumulative = 0;
    //         foreach ($eligiblePrizes as $prize) {
    //             $cumulative += $prize['weight'];
    //             if ($rand <= $cumulative) {
    //                 return $prize;
    //             }
    //         }
    //     }

    //     return ['type' => 'no_prize', 'amount' => null];
    // }
    private function rollPrize(int $currentStep): array
{
    $todayFeedbackCount = Feedback::whereNotNull('completed_at')
        ->whereDate('completed_at', Carbon::today())
        ->count();

    $eligiblePrizes = [];

    if ($currentStep >= 6) {
        if ($todayFeedbackCount >= 100) {
            $eligiblePrizes[] = ['type' => 'voucher_100k', 'amount' => 100000, 'weight' => 5];
        }
        $eligiblePrizes[] = ['type' => 'voucher_50k', 'amount' => 50000, 'weight' => 15];
        $eligiblePrizes[] = ['type' => 'voucher_25k', 'amount' => 25000, 'weight' => 30];
    } else {
        $eligiblePrizes[] = ['type' => 'voucher_50k', 'amount' => 50000, 'weight' => 15];
        $eligiblePrizes[] = ['type' => 'voucher_25k', 'amount' => 25000, 'weight' => 30];
    }

    $roll = random_int(1, 100);
    $totalWeight = array_sum(array_column($eligiblePrizes, 'weight'));

    if (! empty($eligiblePrizes) && $roll <= $totalWeight) {
        $rand = random_int(1, $totalWeight);
        $cumulative = 0;
        foreach ($eligiblePrizes as $prize) {
            $cumulative += $prize['weight'];
            if ($rand <= $cumulative) {
                return $prize;
            }
        }
    }

    return ['type' => 'no_prize', 'amount' => null];
}

    private function generateVoucherCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (FeedbackSpin::where('voucher_code', $code)->exists());

        return $code;
    }
    public function done(Request $request): RedirectResponse
{
    // Hapus session feedback aktif supaya user bisa mulai baru
    $request->session()->forget('feedback_id');

    return redirect()->route('home');
}
}
