<?php

use App\Models\Task;
use App\Models\User;
use App\Enums\TaskStatus;

describe('GET /api/v1/tasks', function () {
    it('returns 401 if not authenticated', function () {
        $response = $this->getJson('/api/v1/tasks');
        $response->assertStatus(401);
    });

    it('returns a list of tasks for admin', function () {
        $admin = User::factory()->create(['is_admin' => true]);
        Task::factory()->count(5)->create();

        $response = $this->actingAs($admin)->getJson('/api/v1/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    });

    it('returns only owned tasks for regular user', function () {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();

        Task::factory()->count(3)->create(['user_id' => $user->id]);
        Task::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    });

    it('can filter tasks by title', function () {
        $user = User::factory()->create();
        Task::factory()->create(['title' => 'Нужная мне таска, йее']);
        Task::factory()->create(['title' => 'Какая-то другая таска']);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?title=нужн');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Нужная мне таска, йее');
    });

    it('can filter tasks by title but returns nothing when no matching tasks', function () {
        $user = User::factory()->create();
        Task::factory()->create(['title' => 'Нужная мне таска, йее']);
        Task::factory()->create(['title' => 'Какая-то другая таска']);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?title=капибара');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    });

    it('can filter tasks by user name', function () {
        $user = User::factory()->create([
            'name' => 'Володя',
        ]);
        $otherUser = User::factory()->create([
            'name' => 'Санёк',
        ]);
        Task::factory()->create(['title' => 'Задача 1', 'user_id' => $otherUser->id]);
        Task::factory()->create(['title' => 'Задача 2', 'user_id' => $user->id, 'created_at' => now()->subMinute()]);
        Task::factory()->create(['title' => 'Задача 3', 'user_id' => $user->id, 'created_at' => now()]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?user_name=волод');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title', 'Задача 3')  // Первым идёт задача, созданная позже
            ->assertJsonPath('data.1.title', 'Задача 2');
    });

    it('can filter tasks by user name but returns nothing when no matching tasks', function () {
        $user = User::factory()->create([
            'name' => 'Володя',
        ]);
        $otherUser = User::factory()->create([
            'name' => 'Санёк',
        ]);
        Task::factory()->create(['title' => 'Задача 1', 'user_id' => $otherUser->id]);
        Task::factory()->create(['title' => 'Задача 2', 'user_id' => $otherUser->id]);
        Task::factory()->create(['title' => 'Задача 3', 'user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?title=волод');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    });

    it('can filter tasks by status', function () {
        $user = User::factory()->create();
        Task::factory()->create(['status' => TaskStatus::PENDING]);
        Task::factory()->create(['status' => TaskStatus::COMPLETED]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?status=' . TaskStatus::COMPLETED->value);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', TaskStatus::COMPLETED->value);
    });

    it('can filter tasks by status but returns nothing when no matching tasks', function () {
        $user = User::factory()->create();
        Task::factory()->create(['status' => TaskStatus::PENDING]);
        Task::factory()->create(['status' => TaskStatus::IN_PROGRESS]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?status=' . TaskStatus::COMPLETED->value);

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    });
});

describe('POST /api/v1/tasks', function () {
    it('returns 401 if not authenticated', function () {
        $response = $this->postJson('/api/v1/tasks');
        $response->assertStatus(401);
    });

    it('can create a task', function () {
        $user = User::factory()->create();

        $dueDate = now()->addDay()->format('Y-m-d');

        $taskData = [
            'title' => 'Доработать дизайн',
            'description' => 'Описание',
            'due_date' => $dueDate,
            'status' => TaskStatus::PENDING->value,
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/tasks', $taskData);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Доработать дизайн')
            ->assertJsonPath('data.description', 'Описание')
            ->assertJsonPath('data.due_date', $dueDate)
            ->assertJsonPath('data.status', TaskStatus::PENDING->value);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Доработать дизайн',
            'user_id' => $user->id,
        ]);
    });

    it('can create a task without due_date', function () {
        $user = User::factory()->create();

        $taskData = [
            'title' => 'Задача без дедлайна',
            'status' => TaskStatus::PENDING->value,
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/tasks', $taskData);

        $response->assertStatus(200)
            ->assertJsonPath('data.due_date', null);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Задача без дедлайна',
            'due_date' => null,
        ]);
    });

    it('validates task creation', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/tasks');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    });
});

describe('GET /api/v1/tasks/{task}', function () {
    it('returns 401 if not authenticated', function () {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/$task->id");

        $response->assertStatus(401);
    });

    it('returns 404 if no task with given id exists', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/v1/tasks/100");

        $response->assertStatus(404);
    });

    it('can show a task', function () {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/v1/tasks/$task->id");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id);
    });

    it('cannot show another user\'s task', function () {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson("/api/v1/tasks/$task->id");

        $response->assertStatus(403);
    });

    it('allows admin to show another user\'s task', function () {
        $admin = User::factory()->create(['is_admin' => true]);
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($admin)->getJson("/api/v1/tasks/$task->id");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id);
    });
});

describe('PUT /api/v1/tasks/{task}', function () {
    it('returns 401 if not authenticated', function () {
        $task = Task::factory()->create();
        $updateData = [
            'title' => 'Обновлённый заголовок',
            'due_date' => now()->addDays(2)->format('Y-m-d'),
            'status' => TaskStatus::COMPLETED->value,
        ];

        $response = $this->putJson("/api/v1/tasks/$task->id", $updateData);

        $response->assertStatus(401);
    });

    it('can update a task', function () {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Старый заголовок',
            'due_date' => now(),
            'status' => TaskStatus::PENDING->value,
        ]);

        $dueDate = now()->addDays(2)->format('Y-m-d');
        $updateData = [
            'title' => 'Обновлённый заголовок',
            'due_date' => $dueDate,
            'status' => TaskStatus::IN_PROGRESS->value,
        ];

        $response = $this->actingAs($user)->putJson("/api/v1/tasks/$task->id", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Обновлённый заголовок')
            ->assertJsonPath('data.due_date', $dueDate)
            ->assertJsonPath('data.status', TaskStatus::IN_PROGRESS->value);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Обновлённый заголовок',
            'due_date' => $dueDate,
            'status' => TaskStatus::IN_PROGRESS->value,
        ]);
    });

    it('cannot update another user\'s task', function () {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->putJson("/api/v1/tasks/$task->id", [
            'title' => 'Йоу',
            'due_date' => now()->format('Y-m-d'),
            'status' => TaskStatus::COMPLETED->value,
        ]);

        $response->assertStatus(403);
    });

    it('allows admin to update another user\'s task', function () {
        $admin = User::factory()->create(['is_admin' => true]);
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);
        $updateData = [
            'title' => 'Обновлённый заголовок',
            'due_date' => now()->addDays(3)->format('Y-m-d'),
            'status' => TaskStatus::COMPLETED->value,
        ];

        $response = $this->actingAs($admin)->putJson("/api/v1/tasks/$task->id", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Обновлённый заголовок');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Обновлённый заголовок',
        ]);
    });
});

describe('DELETE /api/v1/tasks/{task}', function () {
    it('returns 401 if not authenticated', function () {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/v1/tasks/$task->id");

        $response->assertStatus(401);
    });

    it('can delete a task', function () {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/tasks/$task->id");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    });

    it('cannot delete another user\'s task', function () {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/tasks/$task->id");

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    });

    it('allows admin to delete another user\'s task', function () {
        $admin = User::factory()->create(['is_admin' => true]);
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($admin)->deleteJson("/api/v1/tasks/$task->id");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    });
});
