<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

    protected $fillable = ['lesson_id', 'student_id', 'rating', 'comment'];
    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function lesson() {
        return $this->belongsTo(Lesson::class);
    }
}
