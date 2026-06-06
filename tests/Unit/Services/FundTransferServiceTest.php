<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\DTOs\TransferDTO;
use App\Models\Account;
use App\Services\FundTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidTransferException;
use App\Exceptions\AccountNotFoundException;
use App\Models\Transaction;

class FundTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_transfer()
    {
        $from = Account::factory()->create([
            'balance' => 1000
        ]);

        $to = Account::factory()->create([
            'balance' => 500
        ]);

        $dto = new TransferDTO(
            fromAccountId:
                $from->id,

            toAccountId:
                $to->id,

            amount:
                100,

            description:
                'Test Transfer',

            referenceId:
                'TEST001'
        );

        $service =
            app(FundTransferService::class);

        $service->transfer($dto);

        $from->refresh();
        $to->refresh();

        $this->assertEquals(
            900,
            $from->balance
        );

        $this->assertEquals(
            600,
            $to->balance
        );
    }

    public function test_insufficient_balance()
    {
        $from = Account::factory()->create([
            'balance' => 50
        ]);

        $to = Account::factory()->create();

        $dto = new TransferDTO(
            $from->id,
            $to->id,
            100,
            null,
            'FAIL001'
        );

        $this->expectException(\App\Exceptions\InsufficientBalanceException::class
        );

        app(FundTransferService::class)->transfer($dto);
    }

    public function test_same_account_transfer()
    {
        $account =
            Account::factory()->create();

        $dto = new TransferDTO(
            $account->id,
            $account->id,
            100,
            null,
            'FAIL002'
        );

        $this->expectException(\App\Exceptions\InvalidTransferException::class
        );

        app(FundTransferService::class)->transfer($dto);
    }

    public function test_zero_amount_transfer_exception(){
            $from = Account::factory()->create([
            'balance' => 1000
        ]);

        $to = Account::factory()->create([
            'balance' => 500
        ]);

        $dto = new TransferDTO(
            $from->id,
            $to->id,
            0,
            null,
            'ZERO001');

        $this->expectException(InvalidTransferException::class);
        app(FundTransferService::class)->transfer($dto);
    }

    public function test_negative_amount_transfer_exception(){
        $from = Account::factory()->create([
        'balance' => 1000
       ]);

        $to = Account::factory()->create([
        'balance' => 500
        ]);

        $dto = new TransferDTO($from->id,$to->id,-100,null,'NEG001');
        $this->expectException(InvalidTransferException::class);
        app(FundTransferService::class)->transfer($dto);
    }

    public function test_max_amount_exceeded_exception(){
        $from = Account::factory()->create([
        'balance' => 2000000
        ]);

        $to = Account::factory()->create();
        $dto = new TransferDTO($from->id,$to->id,1000000,null,'MAX001');
        $this->expectException(InvalidTransferException::class);

        app(FundTransferService::class)->transfer($dto);
    }

    public function test_account_not_found_exception()
    {
        $dto = new TransferDTO(99999,99998,100,null,'ACC001'
        );

        $this->expectException(
            AccountNotFoundException::class
        );

        app(FundTransferService::class)
            ->transfer($dto);
    }

    public function test_transfer_with_decimals()
    {
        $from = Account::factory()->create([
            'balance' => 1000.75
        ]);

        $to = Account::factory()->create([
            'balance' => 500.25
        ]);

        $dto = new TransferDTO(
            $from->id,
            $to->id,
            100.50,
            null,
            'DEC001'
        );

        app(FundTransferService::class)->transfer($dto);

        $from->refresh();
        $to->refresh();

        $this->assertEquals(
            '900.25',
            number_format($from->balance, 2)
        );

        $this->assertEquals(
            '600.75',
            number_format($to->balance, 2)
        );
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

    public function test_idempotent_transfer()
    {
        $from = Account::factory()->create([
            'balance' => 1000
        ]);

        $to = Account::factory()->create([
            'balance' => 500
        ]);

        $dto = new TransferDTO(
            $from->id,
            $to->id,
            100,
            null,
            'UNIQUE001'
        );

        $service = app(FundTransferService::class);

        $transaction1 = $service->transfer($dto);

        $transaction2 = $service->transfer($dto);

        $from->refresh();
        $to->refresh();

        $this->assertEquals(
            900,
            $from->balance
        );

        $this->assertEquals(
            600,
            $to->balance
        );

        $this->assertEquals(
            1,
            Transaction::count()
        );

        $this->assertEquals(
            $transaction1->id,
            $transaction2->id
        );
    }

    public function test_cannot_reverse_pending_transaction()
    {
        $transaction = Transaction::factory()->create(['status' => 'pending']);

        $this->expectException(
            InvalidTransferException::class
        );

        app(FundTransferService::class)
            ->reverseTransfer(
                $transaction->id
            );
    }

    public function test_concurrent_transfers_with_locking()
    {
        $from =Account::factory()->create([
                'balance' => 100
            ]);

        $to = Account::factory()->create();

        $dto1 = new TransferDTO(
            $from->id,
            $to->id,
            50,
            null,
            'LOCK001'
        );

        $dto2 = new TransferDTO(
            $from->id,
            $to->id,
            50,
            null,
            'LOCK002'
        );

        $service = app(FundTransferService::class);

        $service->transfer($dto1);

        $this->expectException(
            InsufficientBalanceException::class
        );

        $service->transfer($dto2);
    }
}