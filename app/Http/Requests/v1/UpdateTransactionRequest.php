<?php 

namespace App\Http\Requests\v1;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        // This how to make sure is the data user_id in the db is same with token user_id
        $transaction = $this->route('transaction');
        $userWallet = $transaction->userWallet;
        Log::info($userWallet);
        if ($userWallet->user_id != $user->id) {
            return false;
        }
        return $user != null && $user->tokenCan('update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->method();
        if ($method == 'PUT') {
            return [
                'date' => ['required', 'date'],
                'transaction_type_id' => ['required', 'exists:transaction_types,id'],
                // 'user_wallet_id' => ['required', 'exists:user_wallets,id'],
                'value' => ['required', 'numeric'],
                'category' => ['required', 'string'],
                'sub_category' => ['required', 'string'],
            ];
        } else {
            return [
                'date' => ['sometimes', 'required', 'date'],
                'transaction_type_id' => ['sometimes', 'required', 'exists:transaction_types,id'],
                // 'user_wallet_id' => ['sometimes', 'required', 'exists:user_wallets,id'],
                'value' => ['sometimes', 'required', 'numeric'],
                'category' => ['sometimes', 'required', 'string'],
                'sub_category' => ['sometimes', 'required', 'string'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        $dataToMerge = [];

        if ($this->transactionTypeId) {
            $dataToMerge['transaction_type_id'] = $this->transactionTypeId;
        }
        if ($this->userWalletId) {
            $dataToMerge['user_wallet_id'] = $this->userWalletId;
        }
        if ($this->subCategory) {
            $dataToMerge['sub_category'] = $this->subCategory;
        }
        
        if ($dataToMerge) {
            $this->merge($dataToMerge);
        }
    }
}
