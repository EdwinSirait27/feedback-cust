<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackSpin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
      private const TOTAL_STEPS = 7;
    private const MIN_REQUIRED_STEP = 2; // step yang wajib selesai sebelum boleh spin

   public function welcome(): Response
{
    $countries = require base_path('vendor/umpirsky/country-list/data/en/country.php');

    return Inertia::render('Feedback/Welcome', [
        'countries' => collect($countries)
            ->map(fn ($name, $code) => ['code' => $code, 'name' => $name])
            ->sortBy('name')
            ->values(),
    ]);
}
    public function selectLanguage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'language' => ['required', 'string', 'in:id,en,zh,ja,ko'],
        ]);

        $feedback = $this->currentFeedback() ?? $this->createFeedback($request);
        $feedback->update(['language' => $data['language'], 'current_step' => 2]);

        $request->session()->put('feedback_id', $feedback->id);

        return redirect()->route('feedback.step', ['step' => 2]);
    }
private function getCountries(): array
{
    $raw = require base_path('vendor/umpirsky/country-list/data/en/country.php');

    return collect($raw)
        ->map(fn ($name, $code) => ['code' => $code, 'name' => $name])
        ->sortBy('name')
        ->values()
        ->all();
}
    public function showStep(int $step): Response|RedirectResponse
{
    $feedback = $this->currentFeedback();

    if (! $feedback) {
        return redirect()->route('home');
    }

    if ($step > $feedback->current_step) {
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
        $feedback = $this->currentFeedback();

        if (! $feedback) {
            return redirect()->route('feedback.welcome');
        }

        $validated = $request->validate($this->rulesForStep($step, $feedback));

        $feedback->fill($validated);
        $feedback->advanceStep($step);
        $feedback->save();

        $wantsToFinishNow = $request->boolean('finish_now');

        if ($wantsToFinishNow || $step >= self::TOTAL_STEPS) {
            return redirect()->route('feedback.spin');
        }

        return redirect()->route('feedback.step', ['step' => $step + 1]);
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

public function spin(): RedirectResponse
{
    $feedback = $this->currentFeedback();

    abort_if(! $feedback || $feedback->current_step <= self::MIN_REQUIRED_STEP, 403, 'Data wajib belum lengkap.');
    abort_if($feedback->spin()->exists(), 409, 'Sudah pernah spin.');

    $prize = $this->rollPrize();

    $feedback->spin()->create([
        'prize_type' => $prize['type'],
        'voucher_code' => $prize['amount'] ? $this->generateVoucherCode() : null,
        'voucher_amount' => $prize['amount'],
        'spun_at' => now(),
    ]);

    $feedback->update(['completed_at' => now()]);

    return redirect()->route('feedback.spin'); // balik ke spin page, bukan result
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

    private function rulesForStep(int $step, Feedback $feedback): array
    {
        return match ($step) {
            2 => [
                'receipt_number' => ['required', 'string', 'unique:feedbacks,receipt_number,'.$feedback->id],
                'total_purchase' => ['required', 'integer', 'min:0'],
                'country' => ['required', 'string'],
                'overall_rating' => ['required', 'integer', 'between:1,5'],
            ],
            3 => [
                'staff_friendliness' => ['required', 'integer', 'between:1,5'],
                'staff_helpfulness' => ['required', 'integer', 'between:1,5'],
                'checkout_process' => ['required', 'integer', 'between:1,5'],
                'service_speed' => ['required', 'integer', 'between:1,5'],
            ],
            4 => [
                'store_cleanliness' => ['required', 'integer', 'between:1,5'],
                'store_ambience' => ['required', 'integer', 'between:1,5'],
                'product_display' => ['required', 'integer', 'between:1,5'],
                'product_variety' => ['required', 'integer', 'between:1,5'],
            ],
            5 => [
                'product_quality' => ['required', 'integer', 'between:1,5'],
                'product_design' => ['required', 'integer', 'between:1,5'],
                'price_appropriateness' => ['required', 'integer', 'between:1,5'],
                'value_for_money' => ['required', 'integer', 'between:1,5'],
            ],
            6 => [
                'like_most' => ['nullable', 'string', 'max:1000'],
                'improvement_suggestion' => ['nullable', 'string', 'max:1000'],
                'recommend_likelihood' => ['required', 'integer', 'between:0,10'],
            ],
            7 => [
                'may_contact' => ['required', 'boolean'],
                'full_name' => ['nullable', 'string', 'max:255', 'required_if:may_contact,true'],
                'email' => ['nullable', 'email', 'required_if:may_contact,true'],
                'whatsapp' => ['nullable', 'string', 'max:20'],
            ],
            default => [],
        };
    }

    private function rollPrize(): array
    {
        // Sesuaikan probabilitas dengan kebijakan bisnis
        $roll = random_int(1, 100);

        return match (true) {
            $roll <= 5 => ['type' => 'voucher_100k', 'amount' => 100000],
            $roll <= 20 => ['type' => 'voucher_50k', 'amount' => 50000],
            $roll <= 50 => ['type' => 'voucher_25k', 'amount' => 25000],
            default => ['type' => 'no_prize', 'amount' => null],
        };
    }

    private function generateVoucherCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (FeedbackSpin::where('voucher_code', $code)->exists());

        return $code;
    }
}
