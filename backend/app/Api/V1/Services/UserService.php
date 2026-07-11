<?php

namespace App\Api\V1\Services;

use App\Models\User;

class UserService
{
    public function saveUser(array $data, ?User $user = null): User
    {
        if ($user && empty($data['password'])) {
            unset($data['password']);
        }

        if ($user) {
            $user->update($data);
        } else {
            $user = User::query()->create($data);
        }

        return $user;
    }
}
