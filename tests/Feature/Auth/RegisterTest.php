<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
     * Test registration with incorrect values.
     */
    public function testBadRegistrationData()
    {
        $formData = [
            'name'      => $this->faker->name,
            'email'     => 'notAValidEmail',
            'password'  => 'badPas'
        ];

        // Incorrect Login Details
        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/register', $formData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message'   => 'The given data was invalid.',
                'errors'    => [
                    'email' => [
                        'The email must be a valid email address.'
                    ],
                    'password' => [
                        'The password must be at least 8 characters.',
                        'The password confirmation does not match.'
                    ]
                ]
            ]);
    }

    /**
     * Test registration with missing values.
     */
    public function testMissingRegistrationData()
    {
        // Missing Login Details
        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/register', []);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message'   => 'The given data was invalid.',
                'errors'    => [
                    'name' => [
                        'The name field is required.'
                    ],
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
     * Test registration with duplicate email
     */
    public function testRegisterDuplicateEmail()
    {
        $name = $this->faker->name;
        $password = '!@Strong@NdVAliDp@55Word';

        $formData = [
            'name'      => $name,
            'email'     => $this->user->email,
            'password'  => $password,
            'password_confirmation' => $password
        ];

        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/register', $formData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message'   => 'The given data was invalid.',
                'errors'    => [
                    'email' => [
                        'The email has already been taken.'
                    ]
                ]
            ]);
    }

    /**
     * Test registration with password mismatch
     */
    public function testPasswordConfirmationMismatch()
    {
        $password = '!@Strong@NdVAliDp@55Word';
        $formData = [
            'name'      => 'John Smith',
            'email'     => 'john.smith@email.com',
            'password'  => $password,
            'password_confirmation' => 'MismatchedPassword'
        ];

        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/register', $formData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message'   => 'The given data was invalid.',
                'errors'    => [
                    'password' => [
                        'The password confirmation does not match.'
                    ]
                ]
            ]);
    }

    /**
     * Test correct registration.
     *
     * @return void
     */
    public function testCorrectRegistration()
    {
        $name = $this->faker->name;
        $email = $this->faker->safeEmail;
        $password = '!@Strong@NdVAliDp@55Word';
        $formData = [
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
            'password_confirmation' => $password
        ];

        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/register', $formData);

        $response
            ->assertStatus(201)
            ->assertExactJson(['']);
    }
}
