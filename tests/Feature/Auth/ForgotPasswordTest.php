<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ForgotPasswordTest extends TestCase
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
     * Test requesting a password reset with missing email.
     */
    public function testRequestingAPasswordResetWithNoEmail()
    {
        Notification::fake();

        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/forgot-password', []);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message'   => 'The given data was invalid.',
                'errors'    => [
                    'email' => [
                        'The email field is required.'
                    ]
                ]
            ]);

        Notification::assertNotSentTo(
            [$this->user], ResetPassword::class
        );
    }

    /**
     * Test requesting a password reset with incorrect email.
     */
    public function testRequestingAPasswordResetWithNonExistingEmail()
    {
        Notification::fake();

        $formData = [
            'email'     => 'non-existing-email@email.com',
        ];

        // Incorrect Login Details
        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/forgot-password', $formData);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message'   => 'The given data was invalid.',
                'errors'    => [
                    'email' => [
                        'We can\'t find a user with that email address.'
                    ]
                ]
            ]);

        Notification::assertNotSentTo(
            [$this->user], ResetPassword::class
        );
    }

    /**
     * Test requesting a password reset with correct email.
     */
    public function testRequestingAPasswordResetWithExistingEmail()
    {
        Notification::fake();

        $formData = [
            'email'     => $this->user->email,
        ];

        // Incorrect Login Details
        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/forgot-password', $formData);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message'   => 'We have emailed your password reset link!'
            ]);

        Notification::assertSentTo(
            [$this->user], ResetPassword::class
        );
    }
}
