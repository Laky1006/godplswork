<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LessonController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;


// Route::get('/', function () {
//     // return Inertia::render('Welcome', [
//     //     'canLogin' => Route::has('login'),
//     //     'canRegister' => Route::has('register'),
//     //     'laravelVersion' => Application::VERSION,
//     //     'phpVersion' => PHP_VERSION,
//     // ]);


//     // npm run dev
//     // php artisan serve

//  // MAKE IMG FOR LESOSN, SHORT DECS, LONG DESC

//     return Inertia::render('Home');
// });

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [LessonController::class, 'index']);

Route::middleware(['auth'])->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->middleware(['auth'])->name('reviews.store');

});

Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('reviews.destroy');

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware('auth');


Route::get('/about', function () {
    return Inertia::render('About', [
        'auth' => ['user' => auth()->user()],
    ]);
})->name('about');

// Route::get('/my-lessons', function () {
//     $user = auth()->user();

//     if ($user->role === 'teacher') {
//         return Inertia::render('Lessons/TeacherLessons');
//     }

//     if ($user->role === 'student') {
//         return Inertia::render('Lessons/StudentLessons');
//     }

//     abort(403);
// })->middleware(['auth'])->name('my-lessons');

Route::get('/my-lessons', function () {
    $user = Auth::user();

    if ($user->role === 'teacher') {
        $teacherId = $user->teacher->id ?? null;

        if (!$teacherId) {
            abort(403, 'Teacher record not found.');
        }

        $lessons = Lesson::where('teacher_id', $teacherId)->get();

        return Inertia::render('Lessons/TeacherLessons', [
            'lessons' => $lessons,
            'auth' => ['user' => $user],
        ]);
    }

   if ($user->role === 'student') {
        return app(LessonController::class)->studentLessons();
    }

    abort(403);
})->middleware(['auth'])->name('my-lessons');

//Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');

    // Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');

    Route::middleware('auth')->group(function () {
        Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    });


Route::get('/lessons-create', function () {
    return Inertia::render('Lessons/CreateLesson');
})->name('lessons.create');
Route::get('/lessons/{id}', [LessonController::class, 'show'])->name('lessons.show');

Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])->middleware(['auth'])->name('lessons.destroy');



Route::put('/lessons/{id}', [LessonController::class, 'update'])->middleware(['auth'])->name('lessons.update');

Route::post('/lessons/{id}/book', [LessonController::class, 'book'])->middleware(['auth'])->name('lessons.book');

Route::post('/lessons/cancel', [LessonController::class, 'cancel'])->middleware(['auth'])->name('lessons.cancel');

Route::get('/lessons/{id}/edit', [LessonController::class, 'edit'])->middleware(['auth'])->name('lessons.edit');



require __DIR__.'/auth.php';