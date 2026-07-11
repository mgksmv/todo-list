<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\UserUpdatePasswordRequest;
use App\Api\V1\Requests\UserUpdateRequest;
use App\Api\V1\Resources\UserResource;
use App\Api\V1\Services\UserService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

#[Group('Пользователь')]
class UserController extends ApiController
{
    public function __construct(
        protected UserService $userService,
    ) {
    }

    /**
     * Получить текущего пользователя
     */
    public function show(): JsonResponse
    {
        return $this->success(UserResource::make(auth()->user()));
    }

    /**
     * Обновить данные текущего пользователя
     */
    public function update(UserUpdateRequest $request): JsonResponse
    {
        $user = $this->userService->saveUser($request->validated(), auth()->user());

        return $this->success(UserResource::make($user));
    }

    /**
     * Обновить пароль текущего пользователя
     */
    public function updatePassword(UserUpdatePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return $this->success(UserResource::make($user));
    }
}
