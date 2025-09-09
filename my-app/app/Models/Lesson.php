<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    //
    use HasFactory;
    protected $fillable = ['title', 'description', 'rating', 'teacher_id', 'phone', 'banner'];

    protected $casts = [
        'labels' => 'array',
    ];

    public function teacher()
    {
        return $this->belongsTo(\App\Models\Teacher::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'lesson_student')
            ->withPivot('date', 'time')
            ->withTimestamps();
    }

    public function slots()
    {
        return $this->hasMany(\App\Models\LessonSlot::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function updateAverageRating()
    {
        $average = $this->reviews()->avg('rating');
        $this->rating = $average;
        $this->save();
    }

}
