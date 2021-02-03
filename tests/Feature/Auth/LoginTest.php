<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User $user
     */
    public $user;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['password' => bcrypt('password')]);
    }

    /**
     * Test incorrect login.
     */
    public function testIncorrectLoginCredentials()
    {
        $formData = [
            'email'     => 'noexisiting@email.com',
            'password'  => 'wrongP@ssword'
        ];

        // Incorrect Login Details
        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/login', $formData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message'   => 'The given data was invalid.',
                'errors'    => [
                    'email' => [
                        'These credentials do not match our records.'
                    ]
                ]
            ]);
    }

    /**
     * Test Missing Details.
     */
    public function testMissingLoginCredentials()
    {
        // Missing Login Details
        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/login', []);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message'   => 'The given data was invalid.',
                'errors'    => [
                    'email' => [
                        'The email field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                ]
            ]);
    }

    /**
     * Test correct login.
     *
     * @return void
     */
    public function testCorrectLogin()
    {
        $formData = [
            'email'     => $this->user->email,
            'password'  => 'password'
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
