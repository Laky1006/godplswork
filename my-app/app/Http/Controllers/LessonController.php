<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use Inertia\Inertia;
use App\Models\LessonSlot;
use App\Models\Notification;


class LessonController extends Controller
{
    public function index()
    {
        //$lessons = Lesson::all();
        $lessons = Lesson::with('teacher.user')->get(); // eager load teacher + user

        return Inertia::render('Home', [
            'lessons' => $lessons,
        ]);
    }

    // Gets all the needed things to show on show.vue
    public function show($id)
    {
        
        $lesson = Lesson::with(['slots', 'reviews.student.user'])->findOrFail($id);

        return Inertia::render('Lessons/Show', [
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'phone' => $lesson->phone,
                'rating' => $lesson->rating,
                'banner' => $lesson->banner,
                'price' => $lesson->price,
                'slots' => $lesson->slots->map(function ($slot) {
                    return [
                        'date' => $slot->date->format('Y-m-d'), 
                        'time' => $slot->time,
                        'is_available' => $slot->is_available,
                    ];
                }),
            ],

            'reviews' => $lesson->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->diffForHumans(),
                    'student_id' => $review->student_id, 
                    'student' => [
                        'name' => $review->student->user->name ?? 'Unknown',
                        'avatar' => $review->student->user->avatar ?? null,
                    ],
                ];
            }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Lessons/CreateLesson');
    }

    // works new lesson creation on teacher side
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'available_slots' => 'nullable|array',
            'available_slots.*.date' => 'required_with:available_slots|date',
            'available_slots.*.time' => 'required_with:available_slots|date_format:H:i',
            'banner' => 'nullable|image|max:2048',
            'labels' => 'nullable|array',
            'labels.*' => 'string|max:50',
            'price' => 'nullable|numeric|min:0|max:10000',
        ]);

        $data['labels'] = $request->labels ?? [];

        $teacher = auth()->user()->teacher;
        if (!$teacher) abort(403, 'Only teachers can create lessons.');

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'phone' => $request->phone,
            'price' => $request->price,
            'teacher_id' => $teacher->id,
        ];

        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('lesson-banners', 'public');
        }

        $lesson = Lesson::create($data);

        // Save slots
        foreach ($request->available_slots ?? [] as $slot) {
            LessonSlot::create([
                'lesson_id' => $lesson->id,
                'date' => $slot['date'],
                'time' => $slot['time'],
                'is_available' => true,
            ]);
        }

        return redirect()->route('my-lessons')->with('success', 'Lesson created successfully.');
    }

    
    // works student booking ()
    public function book(Request $request, $lessonId)
{
    if (!auth()->user()?->student) {
        session()->flash('debug', 'User is not a student');
        return back();
    }

    $studentId = auth()->user()->student->id;

    $lesson = Lesson::with('teacher.user')->findOrFail($lessonId);

    $slot = LessonSlot::where('lesson_id', $lessonId)
        ->where('date', $request->date)
        ->where('time', $request->time)
        ->where('is_available', true)
        ->first();

    if (!$slot) {
        session()->flash('debug', 'Slot not found or already booked');
        return back();
    }

    $slot->is_available = false;
    $slot->student_id = $studentId;
    $slot->save();

    session()->flash('success', 'You have signed up for this lesson!');
    session()->flash('debug', "Booked slot ID: {$slot->id}, student ID: {$studentId}");

    
    Notification::create([
        'user_id' => $lesson->teacher->user->id,
        'type' => 'lesson_booked',
        'lesson_id' => $lesson->id,
        'lesson_title' => $lesson->title,
        'student_id' => $studentId,
        'student_name' => auth()->user()->name,
        'date' => $request->date,
        'time' => $request->time,
    ]);

    return back();
}

    // get only the lessons this tsudent has signed up for

    public function studentLessons()
    {
        $studentId = auth()->user()->student->id;

        $slots = \App\Models\LessonSlot::with('lesson.teacher.user')
            ->where('student_id', $studentId)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $lessons = $slots->map(function ($slot) {
            $lesson = $slot->lesson;
            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'banner' => $lesson->banner,
                'teacher' => $lesson->teacher->user->name ?? 'Unknown',
                'date' => $slot->date->format('Y-m-d'),
                'time' => $slot->time,
            ];
        });

        return Inertia::render('Lessons/StudentLessons', [
            'lessons' => $lessons,
        ]);
    }

    // cancels lessons, make them avilable in database again
    public function cancel(Request $request)
    {
        

        $studentId = auth()->user()?->student?->id;

        if (!$studentId) {
            return back()->withErrors(['error' => 'Only students can cancel lessons.']);
        }

        $slot = LessonSlot::where('lesson_id', $request->lesson_id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->where('student_id', $studentId)
            ->first();

        if (!$slot) {
            return back()->withErrors(['error' => 'Booking not found.']);
        }

        $slot->student_id = null;
        $slot->is_available = true;
        $slot->save();

        return back()->with('success', 'Lesson booking canceled.');
    }

    // works lesson editing on teacher side
    public function edit($id)
    {
        
        $lesson = Lesson::findOrFail($id);

        // Ensure the logged-in teacher owns the lesson
        if ($lesson->teacher_id !== auth()->user()->teacher->id) {
            abort(403);
        }

        return Inertia::render('Lessons/EditLesson', [
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'phone' => $lesson->phone,
                'price' => $lesson->price,
                'banner' => $lesson->banner,
                'slots' => $lesson->slots->map(fn($slot) => [
                    'date' => $slot->date->format('Y-m-d'),
                    'time' => $slot->time,
                ]),
                'labels' => $lesson->labels ?? [],
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        
        $lesson = Lesson::findOrFail($id);

        // Ensure the logged-in teacher owns this lesson
        if ($lesson->teacher_id !== auth()->user()->teacher->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'required|string|max:20',
            'banner' => 'nullable|image|max:2048',
            'price' => 'nullable|numeric|min:0|max:10000',
        ]);

        $lesson->title = $request->title;
        $lesson->description = $request->description;
        $lesson->phone = $request->phone;
        $lesson->labels = $request->labels ?? [];
        $lesson->price = $request->price;

        if ($request->hasFile('banner')) {
            $lesson->banner = $request->file('banner')->store('lesson-banners', 'public');
        }

        $lesson->save();

        // Clear old slots (if any)
        $lesson->slots()->delete();

        // Add new ones
        foreach ($request->input('available_slots', []) as $slot) {
            $lesson->slots()->create([
                'date' => $slot['date'],
                'time' => $slot['time'],
                'is_available' => true,
                'student_id' => null,
            ]);
        }

        return redirect()->route('my-lessons')->with('success', 'Lesson updated.');
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        // Make sure the authenticated user owns the lesson
        if ($lesson->teacher_id !== auth()->user()->teacher->id) {
            abort(403, 'Unauthorized.');
        }

        $lesson->slots()->delete(); // Delete associated slots first (foreign key constraint)
        $lesson->delete();

        return redirect()->route('my-lessons')->with('success', 'Lesson deleted.');
    }


}
