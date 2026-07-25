<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
   
     public function share(Request $request): array
    {
        // Ambil language dari session feedback yang aktif
        $language = 'en'; // default
        $feedbackId = $request->session()->get('feedback_id');
        if ($feedbackId) {
            $feedback = \App\Models\Feedback::find($feedbackId);
            $language = $feedback?->language ?? 'en';
        }
                app()->setLocale($language);
        // return array_merge(parent::share($request), [
        //     'locale' => $language,
        // ]);
         return array_merge(parent::share($request), [
        'locale' => $language,
        'flash' => [
            'error' => $request->session()->get('error'),
        ],
    ]);
    }
}
