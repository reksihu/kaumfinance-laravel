<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'transactionTypeId' => $this->transaction_type_id,
            'transactionTypeName' => $this->transactionType->name,
            'userWalletId' => $this->user_wallet_id,
            'userWalletName' => $this->userWallet->name,
            'userId' => $this->userWallet->user->id,
            'userName' => $this->userWallet->user->name,
            'value' => $this->value,
            'category' => $this->category,
            'subCategory' => $this->sub_category
        ];
    }
}
