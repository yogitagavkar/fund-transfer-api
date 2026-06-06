<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AccountBalanceResource;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function balance(int $id): JsonResponse {
        $account = Account::find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found'
            ],404);
        }

        return response()->json([
            'success' => true,
            'data' => new AccountBalanceResource($account)
        ]);
    }
}
