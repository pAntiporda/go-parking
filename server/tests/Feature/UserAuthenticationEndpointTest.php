<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;

class UserAuthenticationEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_successfully_register_a_user()
    {
        $user = collect([
            'name' => 'Test Name',
            'email' => 'test@email.com',
            'password' => 'testPassword',
        ]);

        $this
            ->postJson(route('auth.register'), $user->toArray())
            ->assertCreated();

        $this->assertDatabaseHas('users', $user->forget('password')->toArray());
    }

    public function test_it_should_not_register_user_with_invalid_data()
    {
        $user = collect([
            'name' => 'Test Name',
            'email' => 'Invalid Email',
            'password' => 'testPassword',
        ]);

        $this
            ->postJson(route('auth.register'), $user->toArray())
            ->assertJsonValidationErrorFor('email');
    }
}
