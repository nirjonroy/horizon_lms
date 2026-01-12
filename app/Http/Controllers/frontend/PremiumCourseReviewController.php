<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\PremiumCourse;
use App\Models\PremiumCourseReview;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PremiumCourseReviewController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $course = PremiumCourse::where('slug', $slug)->firstOrFail();

        $hasPurchased = Order::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'paid')
            ->whereRaw(
                "JSON_CONTAINS(orders.items, JSON_OBJECT('id', ?), '$')",
                [$course->id]
            )
            ->exists();

        if (! $hasPurchased) {
            return redirect()
                ->route('course.show', $course->slug)
                ->with('error', 'Only enrolled students can review this course.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        PremiumCourseReview::updateOrCreate(
            [
                'premium_course_id' => $course->id,
                'user_id' => $request->user()->id,
            ],
            [
                'rating' => $validated['rating'],
                'review' => $validated['review'],
                'is_approved' => true,
            ]
        );

        return redirect()
            ->route('course.show', $course->slug)
            ->with('success', 'Thanks for sharing your review!');
    }
}
