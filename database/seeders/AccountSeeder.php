<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::insert([
            [
                'account_number' => 'ACC001',
                'account_holder_name' => 'Alice Johnson',
                'balance' => 5000.00,
                'status' => 'active'
            ],
            [
                'account_number' => 'ACC002',
                'account_holder_name' => 'Bob Smith',
                'balance' => 2500.00,
                'status' => 'active'
            ],
            [
                'account_number' => 'ACC003',
                'account_holder_name' => 'Charlie Brown',
                'balance' => 750.00,
                'status' => 'active'
            ],
            [
                'account_number' => 'ACC004',
                'account_holder_name' => 'Dave Wilson',
                'balance' => 1000.00,
                'status' => 'inactive'
            ]
        ]);
    }
}
