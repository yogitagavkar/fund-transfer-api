<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_requires_authentication()
    {
        $response = $this->postJson(
            '/api/v1/transfers',
            []
        );

        $response->assertStatus(401);
    }
}
