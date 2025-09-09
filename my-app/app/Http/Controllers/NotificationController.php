<?php

// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'type' => $note->type,
                    'lesson_title' => $note->lesson_title,
                    'student_name' => $note->student_name,
                    'date' => $note->date,
                    'time' => $note->time,
                    'created_at' => $note->created_at->diffForHumans(),
                ];
            });

        return Inertia::render('Notifications', [
            'notifications' => $notifications,
            'auth' => [
                'user' => $user,
            ],
        ]);
    }
}
