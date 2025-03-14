<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
            'status' => 'pending',
            'priority' => 'low',
            'due_date' => today()->addDay(),
            'user_id' => User::factory(),
        ];
    }
}
