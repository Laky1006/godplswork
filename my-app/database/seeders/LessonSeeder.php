<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Teacher;
use App\Models\LessonSlot;
use Carbon\Carbon;

class LessonSeeder extends Seeder
{
    public function run()
{
    $teachers = Teacher::all();

    if ($teachers->isEmpty()) {
        $this->command->info('No teachers found. Please seed teachers first.');
        return;
    }

    $lessons = [
        [
            'title' => 'Introduction to Algebra',
            'description' => "Master the basics of algebra including:\n\n- Variables\n- Equations\n- Simplifying expressions\n\nPerfect for students who want a solid foundation in math.",
            'phone' => '+371 20000001',
            'price' => 15,
            'labels' => ['Math', 'Algebra'],
        ],
        [
            'title' => 'English Conversation Practice',
            'description' => "Improve your spoken English through:\n\n- Role-playing real-life scenarios\n- Vocabulary building\n- Confidence exercises\n\nGreat for beginners and intermediate learners.",
            'phone' => '+371 20000002',
            'price' => 12,
            'labels' => ['English', 'Language'],
        ],
        [
            'title' => 'Latvian for Beginners',
            'description' => "Get started with Latvian:\n\nThis course covers basic vocabulary, pronunciation and sentence structures. Includes interactive speaking activities to build confidence.\n\n📘 Topics:\n- Alphabet and sounds\n- Everyday conversations\n- Simple grammar rules",
            'phone' => '+371 20000003',
            'price' => 10,
            'labels' => ['Latvian', 'Language'],
        ],
        [
            'title' => 'Biology: Human Body Systems',
            'description' => "Dive into the structure and function of the human body. Understand key systems like:\n\n- Circulatory\n- Nervous\n- Digestive\n\nIdeal for middle/high school science students.",
            'phone' => '+371 20000004',
            'price' => 18,
            'labels' => ['Biology', 'Science'],
        ],
        [
            'title' => 'Creative Writing Workshop',
            'description' => "Unleash your imagination! This workshop helps you:\n\n- Write engaging short stories\n- Develop characters and settings\n- Master narrative structure\n\nSuitable for ages 12+.",
            'phone' => '+371 20000005',
            'price' => 14,
            'labels' => ['English', 'Creative'],
        ],
        [
            'title' => 'Python Coding Basics',
            'description' => "Start your coding journey with Python:\n\n- Syntax and variables\n- Loops and conditions\n- Real-world mini-projects\n\nNo prior experience needed!",
            'phone' => '+371 20000006',
            'price' => 20,
            'labels' => ['Coding', 'Python'],
        ],
        [
            'title' => 'Painting Techniques for Beginners',
            'description' => "Learn acrylic and watercolor techniques:\n\n🎨 Includes:\n- Brush control\n- Color mixing\n- Layering & textures\n\nAll supplies provided in class.",
            'phone' => '+371 20000007',
            'price' => 16,
            'labels' => ['Art', 'Leisure'],
        ],
        [
            'title' => 'Math Games for Kids',
            'description' => "Make math fun and engaging!\n\n- Number puzzles\n- Logical challenges\n- Group competitions\n\nIdeal for ages 8–12.",
            'phone' => '+371 20000008',
            'price' => 9,
            'labels' => ['Math', 'Games'],
        ],
        [
            'title' => 'Science Lab at Home',
            'description' => "Bring science experiments to your kitchen table!\n\n🧪 Activities:\n- Volcanoes\n- Crystal growing\n- Magnetic tricks\n\nSafe, fun, and educational.",
            'phone' => '+371 20000009',
            'price' => 11,
            'labels' => ['Science', 'Fun'],
        ],
        [
            'title' => 'Guitar for Absolute Beginners',
            'description' => "Start playing in your first lesson:\n\n🎸 Learn:\n- Basic chords\n- Strumming patterns\n- Easy songs\n\nBring your own guitar or borrow one in class.",
            'phone' => '+371 20000010',
            'price' => 17,
            'labels' => ['Music', 'Leisure'],
        ],
    ];

    foreach ($lessons as $lessonData) {
        $randomTeacher = $teachers->random();

        $lesson = Lesson::create(array_merge($lessonData, [
            'teacher_id' => $randomTeacher->id,
        ]));

        for ($i = 1; $i <= 3; $i++) {
            LessonSlot::create([
                'lesson_id' => $lesson->id,
                'date' => Carbon::now()->addDays($i)->format('Y-m-d'),
                'time' => '15:00',
                'is_available' => true,
            ]);
        }
    }

    $this->command->info('10 lessons seeded and assigned to random teachers.');
}

}
