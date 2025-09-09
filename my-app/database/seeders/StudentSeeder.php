<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'name' => 'Alise Trololo',
                'username' => 'alice_student',
                'email' => 'alice@example.com',
                'password' => 'Password1!',
                'role' => 'student',
            ],
            [
                'name' => 'John Learner',
                'username' => 'john123',
                'email' => 'john@example.com',
                'password' => 'Learning@1',
                'role' => 'student',
            ],
            [
                'name' => 'Laura Curious',
                'username' => 'curiouslaura',
                'email' => 'laura@example.com',
                'password' => 'Smart#456',
                'role' => 'student',
            ],
        ];

        foreach ($students as $data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);

            Student::create([
                'user_id' => $user->id,
            ]);
        }
    }
}
