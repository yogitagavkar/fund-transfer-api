<?php

namespace App\Repositories;
use App\Models\Account;

class AccountRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function findById(int $id): ?Account{
        return Account::find($id);
    }

     public function lockAccounts(int $fromId,int $toId){
        return Account::whereIn('id',[$fromId, $toId])
        ->orderBy('id')
        ->lockForUpdate()
        ->get();
    }

     public function save(Account $account): void{
        $account->save();
    }
}
