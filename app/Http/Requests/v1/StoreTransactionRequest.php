<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        $userId = $this->user()->id;
        return [
            'date' => ['required', 'date'],
            'transaction_type_id' => ['required', 'exists:transaction_types,id'],
            'user_wallet_id' => ['required', 'exists:user_wallets,id,user_id,' . $userId],
            'user_wallet_id_destination' => ['nullable', 'exists:user_wallets,id,user_id,' . $userId], // This param for Transfer
            'value' => ['required', 'numeric'],
            'category' => ['required', 'string'],
            'sub_category' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'transaction_type_id' => $this->transactionTypeId,
            'user_wallet_id' => $this->userWalletId,
            'user_wallet_id_destination' => $this->userWalletIdDestination,
            'sub_category' => $this->subCategory,
        ]);
    }
}