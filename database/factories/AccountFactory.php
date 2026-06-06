<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_number' =>
                fake()->unique()->numerify(
                    'ACC#####'
                ),

            'account_holder_name' =>
                fake()->name(),

            'balance' =>
                fake()->randomFloat(
                    2,
                    1000,
                    10000
                ),

            'currency' =>
                'USD',

            'status' =>
                'active'
        ];
    }
}
