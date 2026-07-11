<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function update(User $user, Task $task): bool
    {
        return $task->user_id === $user->id || $user->is_admin;
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->user_id === $user->id || $user->is_admin;
    }
}
