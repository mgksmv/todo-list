<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('GET /api/v1/user', function () {
    it('returns 401 if the user is not authenticated', function () {
        $response = $this->getJson('/api/v1/user');

        $response->assertStatus(401);
    });

    it('returns the current authenticated user', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/user');

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
});

describe('PUT /api/v1/user', function () {
    it('returns 401 if the user is not authenticated', function () {
        $updateData = [
            'name' => 'Андрей',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson('/api/v1/user', $updateData);

        $response->assertStatus(401);
    });

    it('can update current user data', function () {
        $user = User::factory()->create([
            'name' => 'Алексей',
            'email' => 'old@example.com',
        ]);
        $updateData = [
            'name' => 'Андрей',
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($user)->putJson('/api/v1/user', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Андрей',
                    'email' => 'updated@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Андрей',
            'email' => 'updated@example.com',
        ]);
    });

    it('validates current user update', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/v1/user', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    });
});

describe('PUT /api/v1/user/password', function () {
    it('returns 401 if the user is not authenticated', function () {
        $response = $this->putJson('/api/v1/user/password', [
            'current_password' => 'password123',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(401);
    });

    it('can update the current user password', function () {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/v1/user/password', [
            'current_password' => 'password123',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
    });

    it('validates password update', function () {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/v1/user/password', [
            'current_password' => 'wrong-password',
            'password' => 'new',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password', 'password']);
    });
});
