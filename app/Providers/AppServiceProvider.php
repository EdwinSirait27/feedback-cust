<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     RateLimiter::for('spin', function (Request $request) {
    //         return [
    //             Limit::perDay(1)
    //                 ->by($request->session()->get('feedback_id'))
    //                 ->response(fn() => response()->json(['message' => 'Sudah spin hari ini.'], 429)),

    //             Limit::perMinute(5)
    //                 ->by($request->ip())
    //                 ->response(fn() => response()->json(['message' => 'Terlalu banyak percobaan.'], 429)),
    //         ];
    //     });

    //     RateLimiter::for('feedback-step', function (Request $request) {
    //         return Limit::perMinute(30)
    //             ->by($request->session()->get('feedback_id') ?? $request->ip());
    //     });
    // }
    public function boot(): void
    {
        // Limiter bernama punya hitungan sendiri; throttle:N,M tanpa nama berbagi satu hitungan per IP
        RateLimiter::for('feedback-read', function (Request $request) {
            return Limit::perMinute(300)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('feedback-action', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Tanpa batas per hari: hitungan bertambah sebelum spin jalan, jadi spin gagal ikut menghabiskan jatah.
        // Satu spin per feedback dijaga oleh lock dan unique index, bukan oleh limiter.
        RateLimiter::for('spin', function (Request $request) {
            $tooManyAttempts = fn () => redirect()->route('feedback.spin')
                ->with('error', 'Too many attempts. Try again later.');

            return [
                Limit::perMinute(5)->by('ip:'.$request->ip())->response($tooManyAttempts),
                Limit::perMinute(5)
                    ->by('feedback:'.($request->session()->get('feedback_id') ?? $request->ip()))
                    ->response($tooManyAttempts),
            ];
        });

        RateLimiter::for('feedback-step', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->session()->get('feedback_id') ?? $request->ip())
                ->response(function () {
                    return redirect()->back()
                        ->with('error', 'Too many attempts. Try again later.');
                });
        });
         if ($this->app->isProduction()) {
        URL::forceScheme('https');
    }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
