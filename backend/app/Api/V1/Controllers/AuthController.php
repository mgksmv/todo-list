<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\LoginRequest;
use App\Api\V1\Resources\UserResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

#[Group('Аутентификация')]
class AuthController extends ApiController
{
    /**
     * Войти в аккаунт
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->firstWhere(['email' => $request->input('email')]);

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return $this->unauthorized('Неверный логин или пароль.');
        }

        $remember = $request->boolean('remember');
        if ($remember) {
            $token = $user->createToken('token');
        } else {
            $token = $user->createToken('token', expiresAt: now()->addHours(12));
        }

        return $this->success([
            'token' => $token->plainTextToken,
            'remember' => $remember,
            'user' => UserResource::make($user),
        ]);
    }

    /**
     * Выйти из аккаунта
     */
    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->success();
    }
}
