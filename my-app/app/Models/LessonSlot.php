<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'date',
        'time',
        'is_available',
        'student_id',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'date' => 'date',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
