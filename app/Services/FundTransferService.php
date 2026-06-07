<?php

namespace App\Services;

use App\DTOs\TransferDTO;
use App\Exceptions\AccountNotActiveException;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidTransferException;
use App\Models\Account;
use App\Models\Transaction;
use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessTransferCompleted;
use Illuminate\Support\Facades\Cache;

class FundTransferService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private AccountRepository $accountRepository,
        private TransactionRepository $transactionRepository)
    {
        
    }

    public function transfer(TransferDTO $dto): Transaction{
        if ($dto->referenceId) {
            $existingTransaction = Transaction::where(
                'reference_id',
                $dto->referenceId
            )->first();

            if ($existingTransaction) {
                return $existingTransaction;
            }
        }

        $lockKey = "transfer_lock:{$dto->referenceId}";

        $lock = Cache::lock($lockKey,30);

        if (!$lock->get()) {
            throw new InvalidTransferException(
                'Transfer already being processed'
            );
        }
        
        try {
            DB::beginTransaction();
            if ($dto->amount <= 0) {
                throw new InvalidTransferException('Transfer amount must be greater than zero.');
            }

            if ($dto->amount > 999999.99) {
                throw new InvalidTransferException('Transfer amount exceeds allowed limit.');
            }

            if ($dto->fromAccountId === $dto->toAccountId) {
               throw new InvalidTransferException('Cannot transfer to same account.');
            }

            $accounts = $this->accountRepository
                ->lockAccounts(
                    $dto->fromAccountId,
                    $dto->toAccountId
                );

            $fromAccount = $accounts
                ->where('id', $dto->fromAccountId)
                ->first();

            $toAccount = $accounts
                ->where('id', $dto->toAccountId)
                ->first();

            if (!$fromAccount || !$toAccount) {
                throw new AccountNotFoundException();
            }

            if ($fromAccount->balance < $dto->amount) {
                throw new InsufficientBalanceException();
            }

            if(($fromAccount->status == 'inactive') || ($toAccount->status == 'inactive')){
                 throw new AccountNotActiveException();
            }

            $fromAccount->balance -= $dto->amount;
            $toAccount->balance += $dto->amount;

            $fromAccount->save();
            $toAccount->save();

            $transaction = Transaction::create([
                'from_account_id' => $dto->fromAccountId,
                'to_account_id' => $dto->toAccountId,
                'amount' => $dto->amount,
                'reference_id' => $dto->referenceId,
                'description' => $dto->description,
                'status' => 'completed',
                'transaction_type' => 'transfer'
            ]);

            DB::commit();

            Log::info('Transfer Successful',['transaction_id' => $transaction->id,'reference_id' => $dto->referenceId,'amount' => $dto->amount]);
            ProcessTransferCompleted::dispatch(['transaction_id' => $transaction->id,'from_account' => $dto->fromAccountId,'to_account' => $dto->toAccountId,'amount' => $dto->amount,'reference_id' => $dto->referenceId]);

            return $transaction;

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Transfer failed',
                [
                    'message' => $e->getMessage(),
                    'from_account' => $dto->fromAccountId,
                    'to_account' => $dto->toAccountId,
                    'amount' => $dto->amount
                ]
            );

            throw $e;
        } finally {
            if (isset($lock)) {
                $lock->release();
            }
        }       
    }

    public function reverseTransfer(int $transactionId): Transaction {
        DB::beginTransaction();
        try {
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                throw new AccountNotFoundException();
            }

            if ($transaction->status !== 'completed') {
                throw new InvalidTransferException('Only completed transactions can be reversed.');
            }

            $accounts = $this->accountRepository->lockAccounts($transaction->from_account_id,$transaction->to_account_id);

            $fromAccount = $accounts->where('id',$transaction->from_account_id)->whereNotIn('status',['inactive','suspended'])->first();
            $toAccount = $accounts->where('id',$transaction->to_account_id)->whereNotIn('status',['inactive','suspended'])->first();

              if (!$fromAccount || !$toAccount) {
                throw new AccountNotFoundException();
            }

            if ($toAccount->balance < $transaction->amount)
            {
                throw new InvalidTransferException(
                    'Destination account does not have sufficient balance for reversal.'
                );
            }

            $fromAccount->balance +=$transaction->amount;
            $toAccount->balance -=$transaction->amount;

            $fromAccount->save();
            $toAccount->save();

            $transaction->status ='reversed';

            $transaction->save();

            DB::commit();

            return $transaction;

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }
}
