<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $fillable = ['user_id', 'grade'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_student')
            ->withPivot('date', 'time')
            ->withTimestamps();
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
}
