<?php

use App\Models\User;

describe('GET /api/v1/users', function () {
    test('it returns paginated users', function () {
        $user = User::factory()->create();
        User::factory()->count(5)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);
    });

    test('it can filter users by name', function () {
        $user = User::factory()->create();
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/users?name=John');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'John Doe');
    });

    test('it returns 401 if user is not authenticated', function () {
        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(401);
    });
});

describe('POST /api/v1/users/all', function () {
    test('it returns all users without pagination', function () {
        $user = User::factory()->create();
        User::factory()->count(5)->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/users/all');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ])
            ->assertJsonMissingPath('meta');
    });

    test('it returns 401 if user is not authenticated', function () {
        $response = $this->postJson('/api/v1/users/all');

        $response->assertStatus(401);
    });
});

describe('POST /api/v1/users', function () {
    test('it can create a user', function () {
        $user = User::factory()->create();
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/v1/users', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'New User',
                    'email' => 'newuser@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
    });

    test('it validates user creation', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
            ]);
    });

    test('it returns 401 if user is not authenticated', function () {
        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(401);
    });
});

describe('GET /api/v1/users/{user}', function () {
    test('it can show a user', function () {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/v1/users/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                ],
            ]);
    });

    test('it returns 401 if user is not authenticated', function () {
        $targetUser = User::factory()->create();

        $response = $this->getJson("/api/v1/users/{$targetUser->id}");

        $response->assertStatus(401);
    });
});

describe('PUT /api/v1/users/{user}', function () {
    test('it can update a user', function () {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/v1/users/{$targetUser->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Updated Name',
                    'email' => 'updated@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
        ]);
    });

    test('it returns 401 if user is not authenticated', function () {
        $targetUser = User::factory()->create();

        $response = $this->putJson("/api/v1/users/{$targetUser->id}", []);

        $response->assertStatus(401);
    });
});

describe('DELETE /api/v1/users/{user}', function () {
    test('it can delete a user', function () {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/v1/users/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    });

    test('it returns 401 if user is not authenticated', function () {
        $targetUser = User::factory()->create();

        $response = $this->deleteJson("/api/v1/users/{$targetUser->id}");

        $response->assertStatus(401);
    });
});
