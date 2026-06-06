<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Account;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'from_account_id' =>
                Account::factory(),

            'to_account_id' =>
                Account::factory(),

            'amount' =>
                fake()->randomFloat(
                    2,
                    10,
                    1000
                ),

            'status' =>
                'completed',

            'reference_id' =>
                fake()->uuid(),
        ];
    }
}
