<?php

namespace App\Repositories;
use App\Models\Transaction;

class TransactionRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findByReference(?string $referenceId): ?Transaction{
        if (!$referenceId) {
            return null;
        }

        return Transaction::where('reference_id',$referenceId)->first();
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }
}
