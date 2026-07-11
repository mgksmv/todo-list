<?php

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

describe('POST /api/v1/auth/login', function () {
    test('user can login with valid credentials', function () {
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => '12345',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => '12345',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'remember',
                    'user',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'remember' => false,
                    'user' => [
                        'id' => $user->id,
                    ],
                ],
            ]);

        expect($response->json('data.token'))
            ->not->toBeEmpty()
            ->and(
                PersonalAccessToken::query()
                    ->where('tokenable_id', $user->id)
                    ->whereNotNull('expires_at')
                    ->exists(),
            )->toBeTrue();
    });

    test('user can login with valid credentials remember login', function () {
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => '12345',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => '12345',
            'remember' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'remember',
                    'user',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'remember' => true,
                    'user' => [
                        'id' => $user->id,
                    ],
                ],
            ]);

        expect($response->json('data.token'))
            ->not->toBeEmpty()
            ->and(
                PersonalAccessToken::query()
                    ->where('tokenable_id', $user->id)
                    ->whereNull('expires_at')
                    ->exists(),
            )->toBeTrue();
    });

    test('user cannot login with invalid credentials', function () {
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => '12345',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test2@test.com',
            'password' => '67890',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'success',
                'message',
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Неверный логин или пароль.',
            ]);

        expect(
            PersonalAccessToken::query()
                ->where('tokenable_id', $user->id)
                ->exists(),
        )->toBeFalse();
    });
});

describe('POST /api/v1/auth/logout', function () {
    test('authenticated user can logout', function () {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => '12345',
        ]);

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => '12345',
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withToken($token)->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    });

    test('unauthenticated user cannot logout', function () {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => '12345',
        ]);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    });
});
