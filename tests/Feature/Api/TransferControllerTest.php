<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransferControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_api_success()
    {
        $from =
            Account::factory()->create([
                'balance' => 200
            ]);

        $to =
            Account::factory()->create([
                'balance' => 500
            ]);

        $payload = [
            'from_account_id' =>
                $from->id,

            'to_account_id' =>
                $to->id,

            'amount' =>
                100,

            'reference_id' =>
                'API001'
        ];

        $response =
            $this->postJson('/api/v1/transfers',
                $payload
            );
        
        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true
            ]);
    }

    public function test_validation_failure()
    {
        $response =
            $this->postJson(
                '/api/v1/transfers',
                []
            );

        $response
            ->assertStatus(422);
    }

    public function test_insufficient_balance_api()
    {
        $from =
            Account::factory()->create([
                'balance' => 10
            ]);

        $to =
            Account::factory()->create();

        $response =
            $this->postJson(
                '/api/v1/transfers',
                [
                    'from_account_id'
                        => $from->id,

                    'to_account_id'
                        => $to->id,

                    'amount'
                        => 100
                ]
            );

        $response
            ->assertStatus(400);
    }

    public function test_transfer_same_account_validation()
    {
        $account = Account::factory()->create();

        $response = $this->postJson(
            '/api/v1/transfers',
            [
                'from_account_id' => $account->id,
                'to_account_id' => $account->id,
                'amount' => 100
            ]
        );

        $response->assertStatus(422);
    }

    public function test_transfer_validation_invalid_amount()
    {
        $from = Account::factory()->create();
        $to = Account::factory()->create();

        $response = $this->postJson(
            '/api/v1/transfers',
            [
                'from_account_id' => $from->id,
                'to_account_id' => $to->id,
                'amount' => -100
            ]
        );

        $response->assertStatus(422);
    }

    public function test_transfer_invalid_decimal_format()
    {
        $from =
            Account::factory()->create();

        $to =
            Account::factory()->create();

        $response =
            $this->postJson('/api/v1/transfers',
                [
                    'from_account_id' =>
                        $from->id,

                    'to_account_id' =>
                        $to->id,

                    'amount' =>
                        100.999
                ]
            );

        $response
            ->assertStatus(422);
    }
    public function test_get_balance_non_existent_account()
    {
        $response =
            $this->getJson(
                '/api/v1/accounts/99999/balance'
            );

        $response
            ->assertStatus(404);
    }
}