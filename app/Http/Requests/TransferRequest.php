<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_account_id' => [
                'required',
                'integer',
                'exists:accounts,id'
            ],

            'to_account_id' => [
                'required',
                'integer',
                'different:from_account_id',
                'exists:accounts,id'
            ],

            'amount' => [
                'required',
                'numeric',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],

            'description' => [
                'nullable',
                'string',
                'max:500'
            ],

            'reference_id' => [
                'nullable',
                'string',
                'max:100'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'to_account_id.different' =>
                'Source and destination accounts cannot be same.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422)
        );
    }
}