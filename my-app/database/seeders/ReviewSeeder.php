<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Review;
use App\Models\Student;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with('user')->get();
        $lessons = Lesson::all();

        if ($students->isEmpty() || $lessons->isEmpty()) {
            $this->command->info('No students or lessons found. Seed those first.');
            return;
        }

        foreach ($lessons as $lesson) {
            // picks some students to review this lesson
            $reviewers = $students->random(min($students->count(), rand(2, 4)));

            foreach ($reviewers as $student) {
                // avoids duplicates
                if (Review::where('lesson_id', $lesson->id)->where('student_id', $student->id)->exists()) {
                    continue;
                }

                Review::create([
                    'lesson_id' => $lesson->id,
                    'student_id' => $student->id,
                    'rating' => rand(3, 5),
                    'comment' => rand(0, 1) ? $this->randomComment() : null,
                ]);
            }

            $average = Review::where('lesson_id', $lesson->id)->avg('rating');
            $lesson->rating = round($average, 1);
            $lesson->save();
        }
    }

    private function randomComment(): string
    {
        $samples = [
            "Great lesson! Very informative and well-structured.",
            "The teacher was kind and helpful. Highly recommend.",
            "I learned a lot in a short time. :)",
            "Could be improved with more examples, but still good.",
            "Excellent session! Looking forward to more.",
            "very bad. "
        ];

        return $samples[array_rand($samples)];
    }
}
