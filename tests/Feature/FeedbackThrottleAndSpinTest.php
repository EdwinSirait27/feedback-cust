<?php

use App\Mail\VoucherMail;
use App\Models\DailyPrizeQuota;
use App\Models\Feedback;
use App\Models\FeedbackSpin;
use App\Models\VoucherBelanja;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config(['database.connections.mysql_second' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
    DB::purge('mysql_second');

    expect(DB::connection()->getDriverName())->toBe('sqlite')
        ->and(DB::connection()->getDatabaseName())->toBe(':memory:')
        ->and(DB::connection('mysql_second')->getDriverName())->toBe('sqlite');

    Schema::connection('mysql_second')->create('jual_head', function ($table) {
        $table->id();
        $table->date('TGL');
        $table->string('NO_EJ');
        $table->bigInteger('NETTO');
        $table->timestamps();
    });

    createVoucherTable();
});

function createVoucherTable(): void
{
    Schema::connection('mysql_second')->create('voucher_belanja', function ($table) {
        $table->string('NO_VCHR');

        foreach (['ID', 'TGL', 'EXPIRED', 'MEMBER', 'KET', 'NILAI_VOUCHER', 'TARIK', 'NO_EJB', 'TGLTRX', 'BO', 'NO_EJ'] as $column) {
            $table->string($column)->nullable();
        }
    });
}

/**
 * @param  array{voucher_100k_used?: int, voucher_50k_used?: int, voucher_25k_used?: int}  $used
 */
function seedTodayQuota(array $used = []): void
{
    DB::table('daily_prize_quotas')->insert([
        'date' => Carbon::today()->toDateString(),
        'voucher_100k_used' => $used['voucher_100k_used'] ?? 0,
        'voucher_50k_used' => $used['voucher_50k_used'] ?? 0,
        'voucher_25k_used' => $used['voucher_25k_used'] ?? 0,
    ]);
}

function seedExhaustedQuota(): void
{
    seedTodayQuota(['voucher_100k_used' => 2, 'voucher_50k_used' => 2, 'voucher_25k_used' => 4]);
}

function startSurveyWithoutReceipt(): Feedback
{
    test()->flushSession();
    test()->post(route('feedback.language'), ['language' => 'id'])->assertRedirect();
    test()->post(route('feedback.step.update', ['step' => 2]), [
        'has_receipt' => false,
        'country' => 'ID',
        'overall_rating' => 4,
    ])->assertRedirect(route('feedback.step', ['step' => 3]));

    return Feedback::query()->latest('id')->firstOrFail();
}

test('page views do not use up the budget of language and done posts', function () {
    foreach (range(1, 15) as $attempt) {
        $this->get(route('home'))->assertOk();
    }

    $this->post(route('feedback.language'), ['language' => 'id'])
        ->assertRedirect(route('feedback.step', ['step' => 2]));
});

test('page views are allowed well past the old limit of sixty a minute', function () {
    foreach (range(1, 70) as $attempt) {
        $this->get(route('home'))->assertOk();
    }
});

test('page views are still limited', function () {
    foreach (range(1, 300) as $attempt) {
        $this->get(route('home'))->assertOk();
    }

    $this->get(route('home'))->assertTooManyRequests();
});

test('language and done posts are limited to sixty a minute together', function () {
    foreach (range(1, 60) as $attempt) {
        $this->post(route('feedback.done'))->assertRedirect(route('home'));
    }

    $this->post(route('feedback.language'), ['language' => 'id'])->assertTooManyRequests();
});

test('spinning twice keeps one spin and shows the result instead of an error', function () {
    seedExhaustedQuota();
    $feedback = startSurveyWithoutReceipt();

    $this->post(route('feedback.spin.submit'))
        ->assertRedirect(route('feedback.spin'))
        ->assertSessionMissing('error');

    $this->post(route('feedback.spin.submit'))
        ->assertRedirect(route('feedback.spin'))
        ->assertSessionMissing('error');

    expect(FeedbackSpin::query()->where('feedback_id', $feedback->id)->count())->toBe(1);
});

test('the database refuses a second spin for one feedback', function () {
    $feedback = startSurveyWithoutReceipt();
    $spin = ['prize_type' => 'no_prize', 'spun_at' => now()];

    $feedback->spin()->create($spin);

    expect(fn () => $feedback->spin()->create($spin))
        ->toThrow(UniqueConstraintViolationException::class);
});

test('a failed voucher write rolls the spin back and the guest can spin again', function () {
    seedTodayQuota();
    Schema::connection('mysql_second')->drop('voucher_belanja');
    $this->withoutMiddleware(ThrottleRequests::class);
    $this->withoutExceptionHandling();

    $failedFeedback = null;

    foreach (range(1, 80) as $attempt) {
        $feedback = startSurveyWithoutReceipt();

        try {
            $this->post(route('feedback.spin.submit'));
        } catch (QueryException) {
            $failedFeedback = $feedback;

            break;
        }
    }

    expect($failedFeedback)->not->toBeNull()
        ->and(FeedbackSpin::query()->where('feedback_id', $failedFeedback->id)->exists())->toBeFalse()
        ->and($failedFeedback->fresh()->completed_at)->toBeNull();

    $quota = DailyPrizeQuota::query()->firstOrFail();

    expect($quota->voucher_50k_used + $quota->voucher_25k_used)->toBe(0);

    createVoucherTable();

    $this->post(route('feedback.spin.submit'))->assertRedirect(route('feedback.spin'));

    expect(FeedbackSpin::query()->where('feedback_id', $failedFeedback->id)->count())->toBe(1);
});

test('a winning spin writes one voucher, counts the quota and queues one email', function () {
    Mail::fake();
    Storage::fake('local');
    seedTodayQuota();
    $this->withoutMiddleware(ThrottleRequests::class);

    $winningSpin = null;

    foreach (range(1, 80) as $attempt) {
        $feedback = startSurveyWithoutReceipt();
        $feedback->update(['email' => 'tamu@example.test']);

        $this->post(route('feedback.spin.submit'))->assertRedirect(route('feedback.spin'));

        $spin = $feedback->spin()->first();

        if ($spin->prize_type !== 'no_prize') {
            $winningSpin = $spin;

            break;
        }
    }

    expect($winningSpin)->not->toBeNull()
        ->and(VoucherBelanja::query()->where('NO_VCHR', $winningSpin->voucher_code)->count())->toBe(1)
        ->and(VoucherBelanja::query()->count())->toBe(1);

    $quota = DailyPrizeQuota::query()->firstOrFail();

    expect($quota->voucher_100k_used + $quota->voucher_50k_used + $quota->voucher_25k_used)->toBe(1);

    Mail::assertQueued(VoucherMail::class, 1);
});

test('a receipt held by an unfinished survey gets a validation message, not a server error', function () {
    DB::connection('mysql_second')->table('jual_head')->insert([
        'TGL' => Carbon::today()->toDateString(),
        'NO_EJ' => 'R-1',
        'NETTO' => 1000,
    ]);

    $body = ['has_receipt' => true, 'receipt_number' => 'R-1', 'total_purchase' => 1000, 'country' => 'ID', 'overall_rating' => 4];

    $this->post(route('feedback.language'), ['language' => 'id']);
    $this->post(route('feedback.step.update', ['step' => 2]), $body)
        ->assertRedirect(route('feedback.step', ['step' => 3]));

    $this->flushSession();

    $this->post(route('feedback.language'), ['language' => 'id']);
    $this->post(route('feedback.step.update', ['step' => 2]), $body)
        ->assertSessionHasErrors('receipt_number');
});

test('a step submitted twice only earns its points once', function () {
    $feedback = startSurveyWithoutReceipt();
    $ratings = ['staff_friendliness' => 5, 'staff_helpfulness' => 5, 'checkout_process' => 5, 'service_speed' => 5];

    $this->post(route('feedback.step.update', ['step' => 3]), $ratings);
    $this->post(route('feedback.step.update', ['step' => 3]), $ratings);

    expect($feedback->fresh()->total_points)->toBe(30)
        ->and($feedback->fresh()->current_step)->toBe(4);
});

test('changing language keeps survey progress and does not earn points twice', function () {
    $feedback = startSurveyWithoutReceipt();

    $this->post(route('feedback.step.update', ['step' => 3]), [
        'staff_friendliness' => 5,
        'staff_helpfulness' => 5,
        'checkout_process' => 5,
        'service_speed' => 5,
    ]);

    expect($feedback->fresh()->current_step)->toBe(4)
        ->and($feedback->fresh()->total_points)->toBe(30);

    $this->post(route('feedback.language'), ['language' => 'en'])
        ->assertRedirect(route('feedback.step', ['step' => 2]));

    expect($feedback->fresh()->language)->toBe('en')
        ->and($feedback->fresh()->current_step)->toBe(4);

    $this->post(route('feedback.step.update', ['step' => 2]), [
        'has_receipt' => false,
        'country' => 'ID',
        'overall_rating' => 4,
    ])->assertRedirect(route('feedback.step', ['step' => 3]));

    expect($feedback->fresh()->total_points)->toBe(30)
        ->and($feedback->fresh()->current_step)->toBe(4);
});

test('a new survey still starts at step two', function () {
    $this->post(route('feedback.language'), ['language' => 'id'])
        ->assertRedirect(route('feedback.step', ['step' => 2]));

    expect(Feedback::query()->latest('id')->firstOrFail()->current_step)->toBe(2);
});

test('contact details left empty return translated errors for the fields', function () {
    startSurveyWithoutReceipt();

    $this->from(route('feedback.step', ['step' => 7]))
        ->post(route('feedback.step.update', ['step' => 7]), [
            'may_contact' => true,
            'full_name' => '',
            'email' => '',
            'whatsapp' => '0812',
            'finish_now' => true,
        ])
        ->assertRedirect(route('feedback.step', ['step' => 7]))
        ->assertSessionHasErrors(['full_name', 'email']);

    $errors = session('errors')->getBag('default');

    expect($errors->first('full_name'))->not->toContain('validation.')
        ->and($errors->first('email'))->not->toContain('validation.');
});

test('a guest with a receipt can continue past step two', function () {
    DB::connection('mysql_second')->table('jual_head')->insert([
        'TGL' => Carbon::today()->toDateString(),
        'NO_EJ' => 'R-2',
        'NETTO' => 250000,
    ]);

    $this->post(route('feedback.language'), ['language' => 'id']);

    $this->post(route('feedback.step.update', ['step' => 2]), [
        'has_receipt' => true,
        'receipt_number' => 'R-2',
        'total_purchase' => 1,
        'country' => 'ID',
        'overall_rating' => 4,
    ])->assertRedirect(route('feedback.step', ['step' => 3]));

    $feedback = Feedback::query()->latest('id')->firstOrFail();

    expect($feedback->has_receipt)->toBeTrue()
        ->and($feedback->receipt_number)->toBe('R-2')
        ->and($feedback->total_purchase)->toBe(250000);
});

/**
 * The cheaper prizes are used up, so the top prize is the only voucher a spin can still win.
 */
function seedOnlyTopPrizeWinnable(): void
{
    seedTodayQuota(['voucher_50k_used' => 2, 'voucher_25k_used' => 4]);

    foreach (range(1, 100) as $number) {
        Feedback::query()->create(['session_token' => "completed-{$number}", 'completed_at' => now()]);
    }
}

/**
 * @param  array<string, int>  $answers
 */
function spinUntilTopPrizeOrGiveUp(array $answers): bool
{
    test()->withoutMiddleware(ThrottleRequests::class);

    foreach (range(1, 400) as $attempt) {
        $feedback = Feedback::query()->create([
            'session_token' => "guest-{$attempt}",
            'current_step' => 8,
            ...$answers,
        ]);

        test()->withSession(['feedback_id' => $feedback->id])
            ->post(route('feedback.spin.submit'))
            ->assertRedirect(route('feedback.spin'));

        if ($feedback->spin()->first()->prize_type === 'voucher_100k') {
            return true;
        }
    }

    return false;
}

/**
 * @return array<string, int>
 */
function answersThroughStepFive(): array
{
    return [
        'overall_rating' => 4,
        'staff_friendliness' => 5, 'staff_helpfulness' => 5, 'checkout_process' => 5, 'service_speed' => 5,
        'store_cleanliness' => 5, 'store_ambience' => 5, 'product_display' => 5, 'product_variety' => 5,
        'product_quality' => 5, 'product_design' => 5, 'price_appropriateness' => 5, 'value_for_money' => 5,
    ];
}

test('a guest who skipped to the end cannot win the top prize', function () {
    seedOnlyTopPrizeWinnable();

    expect(spinUntilTopPrizeOrGiveUp(['overall_rating' => 4]))->toBeFalse();
});

test('a guest who stopped before step six cannot win the top prize', function () {
    seedOnlyTopPrizeWinnable();

    expect(spinUntilTopPrizeOrGiveUp(answersThroughStepFive()))->toBeFalse();
});

test('a guest who answered through step six can win the top prize', function () {
    seedOnlyTopPrizeWinnable();

    expect(spinUntilTopPrizeOrGiveUp([...answersThroughStepFive(), 'recommend_likelihood' => 0]))->toBeTrue();
});

test('a score of zero on step six counts as answered', function () {
    $feedback = new Feedback([...answersThroughStepFive(), 'recommend_likelihood' => 0]);

    expect($feedback->hasAnsweredThroughStepSix())->toBeTrue();

    $feedback->recommend_likelihood = null;

    expect($feedback->hasAnsweredThroughStepSix())->toBeFalse();
});
