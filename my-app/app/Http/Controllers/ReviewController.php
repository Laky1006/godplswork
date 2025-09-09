<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Review;
use App\Models\Notification;

class ReviewController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'lesson_id' => 'required|exists:lessons,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    $studentId = auth()->user()->student->id ?? null;

    if (!$studentId) {
        return back()->withErrors(['Only students can leave reviews.']);
    }

    $lessonId = $request->lesson_id;

    // Prevent duplicate reviews
    $existing = Review::where('lesson_id', $lessonId)
        ->where('student_id', $studentId)
        ->first();

    if ($existing) {
        return back()->withErrors(['You have already reviewed this lesson.']);
    }

    $review = Review::create([
        'lesson_id' => $lessonId,
        'student_id' => $studentId,
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    $lesson = Lesson::find($review->lesson_id);
    $lesson->updateAverageRating();

    Notification::create([
    'user_id' => $lesson->teacher->user->id,
    'type' => 'review_left',
    'lesson_id' => $lesson->id,
    'lesson_title' => $lesson->title,
    'student_id' => auth()->user()->student->id ?? null,
    'student_name' => auth()->user()->name,
]);

    return back()->with('success', 'Review posted successfully.');
}

public function destroy($id)
{
    $review = Review::findOrFail($id);

    // Ensure the logged-in user is the one who posted it
    if ($review->student_id !== auth()->user()->student->id) {
        abort(403, 'Unauthorized to delete this review.');
    }

    $lesson = $review->lesson; 
    $review->delete();
    $lesson->updateAverageRating();

    return back()->with('success', 'Review deleted.');
}


}
