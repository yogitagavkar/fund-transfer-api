<?php

namespace App\Http\Controllers;

use App\DTOs\TransferDTO;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\TransactionResource;
use App\Services\FundTransferService;
use Illuminate\Http\JsonResponse;
use App\Models\Transaction;

class TransferController extends Controller
{
    public function __construct(
        private FundTransferService $fundTransferService
    ) {
    }

    public function store(TransferRequest $request): JsonResponse {

        $dto = new TransferDTO(
            fromAccountId:
                $request->from_account_id,

            toAccountId:
                $request->to_account_id,

            amount:
                $request->amount,

            description:
                $request->description,

            referenceId:
                $request->reference_id
        );

        $transaction =$this->fundTransferService->transfer($dto);

        return response()->json([
            'success' => true,
            'message' => 'Transfer completed successfully',
            'data' => new TransactionResource($transaction)], 201);
    }

    public function reverse(int $id,FundTransferService $service): JsonResponse
    {
        $transaction = $service->reverseTransfer($id);

        return response()->json([
            'success' => true,
            'message' => 'Transfer reversed',
            'data' => new TransactionResource($transaction)], 201);
    }
    
    public function show(int $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ],404);
        }

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction)], 201);
    }
}