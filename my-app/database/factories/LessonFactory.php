<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'rating' => $this->faker->randomFloat(1, 3, 5),
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
