<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        try {
            $dueDate = Carbon::createFromTimestamp(
                random_int(now()->addHours(3)->timestamp, now()->addMonths(2)->timestamp)
            );
        } catch (\Throwable) {
            $dueDate = now()->addHour();
        }

        return [
            'user_id' => User::count() ? User::inRandomOrder()->first()->id : User::factory(),
            'title' => fake()->word(),
            'description' => fake()->text(),
            'due_date' => $dueDate,
            'status' => Arr::random(TaskStatus::cases()),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
