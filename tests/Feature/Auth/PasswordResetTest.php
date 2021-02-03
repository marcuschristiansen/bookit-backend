<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var User $user
     */
    public User $user;

    /**
     * @var User $userTwo
     */
    public User $userTwo;

    /**
     * @var string $token
     */
    public string $token;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['password' => bcrypt('password')]);
        $this->userTwo = User::factory()->create(['password' => bcrypt('password')]);
        $this->token = Password::broker()->createToken($this->user);
    }

    public function testResettingIncorrectEmail()
    {
        $newPassword = 'Hn^8N64K9LL!@';
        $formData = [
            'email'                 => $this->userTwo->email,
            'token'                 => $this->token,
            'password'              => $newPassword,
            'password_confirmation' => $newPassword
        ];

        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/reset-password', $formData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message'   => 'The given data was invalid.',
                'errors'    => [
                    'email' => [
                        'This password reset token is invalid.'
                    ]
                ]
            ]);
    }

    /**
     * Test successfully resetting the users password
     */
    public function testResetUserPassword()
    {
        $newPassword = 'Hn^8N64K9LL!@';
        $formData = [
            'email'                 => $this->user->email,
            'token'                 => $this->token,
            'password'              => $newPassword,
            'password_confirmation' => $newPassword
        ];

        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/reset-password', $formData);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message'   => 'Your password has been reset!'
            ]);

        // Test logging in with new password
        $formData = [
            'email'     => $this->user->email,
            'password'  => $newPassword
        ];

        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/login', $formData);

        $response
            ->assertStatus(200)
            ->assertExactJson(['two_factor' => false]);

    }
}
