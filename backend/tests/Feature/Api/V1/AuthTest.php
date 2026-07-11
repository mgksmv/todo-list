<?php

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

describe('POST /api/v1/auth/login', function () {
    it('can login with valid credentials', function () {
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
                'message',
                'data' => [
                    'token',
                    'remember',
                    'user',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => null,
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

    it('can login with valid credentials and remember login', function () {
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
                'message',
                'data' => [
                    'token',
                    'remember',
                    'user',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => null,
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

    it('cannot login with invalid credentials', function () {
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
                'message' => __('auth.failed'),
            ]);

        expect(
            PersonalAccessToken::query()
                ->where('tokenable_id', $user->id)
                ->exists(),
        )->toBeFalse();
    });
});

describe('POST /api/v1/auth/logout', function () {
    it('allows authenticated user to logout', function () {
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
                'message' => null,
            ]);
    });

    it('returns 401 when user is not authenticated', function () {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => '12345',
        ]);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    });
});
