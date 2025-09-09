<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $users = [
            [
                'name' => 'Test Teacher',
                'username' => 'test_',
                'email' => 'test2025@example.com',
                'password' => '12345678',
                'role' => 'teacher',
            ],
            [
                'name' => 'Bob Bobly',
                'username' => 'bob_teacher',
                'email' => 'bob@example.com',
                'password' => '12345678',
                'role' => 'teacher',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);

            Teacher::create([
                'user_id' => $user->id,
                'education' => 'Master in Education',
                'bio' => 'Experienced teacher in various subjects.',
            ]);
        }
    }
}
