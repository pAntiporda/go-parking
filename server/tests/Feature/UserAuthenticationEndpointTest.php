<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

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



    public function test_it_should_login_successfully()
    {
        $userPassword = 'testPassword';
        $user = User::create([
            'name' => 'Test Name',
            'email' => 'test@email.com',
            'password' => bcrypt($userPassword),
        ]);

        $this
            ->postJson(route('auth.login'), [
                'email' => $user->email,
                'password' => $userPassword,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'web_token']);
    }

    public function test_it_should_not_login_with_invalid_data()
    {
        $userPassword = 'testPassword';

        User::create([
            'name' => 'Test Name',
            'email' => 'test@email.com',
            'password' => bcrypt($userPassword),
        ]);

        $this
            ->postJson(route('auth.login'), [
                'email' => 'Test Email',
                'password' => $userPassword,
            ])
            ->assertJsonValidationErrorFor('email');
    }

    public function test_it_should_not_login_with_incorrect_credentials()
    {
        $user = User::create([
            'name' => 'Test Name',
            'email' => 'test@email.com',
            'password' => bcrypt('testPassword'),
        ]);

        $this
            ->postJson(route('auth.login'), [
                'email' => $user->email,
                'password' => 'wrongPassword',
            ])
            ->assertUnauthorized();
    }

    public function test_it_should_logout_current_authenticated_user()
    {
        $user = User::create([
            'name' => 'Test Name',
            'email' => 'test@email.com',
            'password' => bcrypt('testPassword'),
        ]);

        Sanctum::actingAs($user);

        $this
            ->postJson(route('auth.logout'))
            ->assertNoContent();
    }
}
