<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    //
    protected $fillable = ['user_id', 'education', 'bio'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function lessons()
    {
        return $this->hasMany(\App\Models\Lesson::class);
    }
}
