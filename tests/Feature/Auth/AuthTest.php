<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test the csrf cookie endpoint.
     *
     * @return void
     */
    public function testCsrf()
    {
        $response = $this->get('/sanctum/csrf-cookie');

        $response->assertStatus(204);
    }
}
