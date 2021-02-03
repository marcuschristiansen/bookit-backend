<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class LogoutTest extends TestCase
{
    /**
     * Logout user.
     */
    public function testLogoutUser()
    {
        $response = $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->json('POST', env('APP_URL') . '/logout', []);

        $response
            ->assertStatus(204);
    }
}

