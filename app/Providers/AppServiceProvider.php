<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

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
    public function boot(): void
    {
        RateLimiter::for('spin', function (Request $request) {
            return [
                Limit::perDay(1)
                    ->by($request->session()->get('feedback_id'))
                    ->response(fn() => response()->json(['message' => 'Sudah spin hari ini.'], 429)),

                Limit::perMinute(5)
                    ->by($request->ip())
                    ->response(fn() => response()->json(['message' => 'Terlalu banyak percobaan.'], 429)),
            ];
        });

        RateLimiter::for('feedback-step', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->session()->get('feedback_id') ?? $request->ip());
        });
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
