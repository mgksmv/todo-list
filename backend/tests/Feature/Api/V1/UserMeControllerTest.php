<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('GET /api/v1/users/me', function () {
    test('it returns current authenticated user', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/v1/users/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    });

    test('it returns 401 if user is not authenticated', function () {
        $response = $this->getJson('/api/v1/users/me');

        $response->assertStatus(401);
    });
});

describe('PUT /api/v1/users/me', function () {
    test('it can update current user data', function () {
        $user = User::factory()->create();
        $updateData = [
            'name' => 'Updated Me',
            'email' => 'updatedme@example.com',
        ];

        $response = $this->actingAs($user)
            ->putJson('/api/v1/users/me', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Updated Me',
                    'email' => 'updatedme@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Me',
            'email' => 'updatedme@example.com',
        ]);
    });

    test('it validates current user update', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson('/api/v1/users/me', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
            ]);
    });

    test('it returns 401 if user is not authenticated', function () {
        $response = $this->putJson('/api/v1/users/me', []);

        $response->assertStatus(401);
    });
});

describe('PUT /api/v1/users/me/password', function () {
    test('it can update current user password', function () {
        $user = User::factory()->create([
            'password' => Hash::make('old_password'),
        ]);
        $passwordData = [
            'current_password' => 'old_password',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ];

        $response = $this->actingAs($user)
            ->putJson('/api/v1/users/me/password', $passwordData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $user->refresh();
        expect(Hash::check('new_password123', $user->password))->toBeTrue();
    });

    test('it validates password update', function () {
        $user = User::factory()->create();
        $passwordData = [
            'current_password' => 'wrong_password',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ];

        $response = $this->actingAs($user)
            ->putJson('/api/v1/users/me/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'current_password',
                'password',
            ]);
    });

    test('it returns 401 if user is not authenticated', function () {
        $response = $this->putJson('/api/v1/users/me/password', []);

        $response->assertStatus(401);
    });
});
