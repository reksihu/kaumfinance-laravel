<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return true;
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        $userId = $this->user()->id;
        return [
            'name' => ['required', 'string', 'unique:user_wallets,name,' . $userId],
            'messages' => [
                'name.unique' => 'A user with this name already exists for your account.',
            ],
        ];
    }
}
