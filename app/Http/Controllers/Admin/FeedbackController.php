<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function index(Request $request): Response
    {
        $feedbacks = Feedback::query()
            ->whereNotNull('completed_at')
            ->when($request->string('search')->toString(), fn ($query, $search) => $query
                ->where('receipt_number', 'like', "%{$search}%")
                ->orWhere('full_name', 'like', "%{$search}%"))
            ->latest('completed_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Feedbacks/Index', [
            'feedbacks' => $feedbacks,
            'filters' => $request->only('search'),
        ]);
    }

    public function show(Feedback $feedback): Response
    {
        $feedback->load('spin');

        return Inertia::render('Admin/Feedbacks/Show', [
            'feedback' => $feedback,
        ]);
    }
}