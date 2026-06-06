<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountBalanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'account_id' =>
                $this->id,

            'account_number' =>
                $this->account_number,

            'account_holder_name' =>
                $this->account_holder_name,

            'balance' =>
                $this->balance,

            'currency' =>
                $this->currency,

            'status' =>
                $this->status
        ];
    }
}