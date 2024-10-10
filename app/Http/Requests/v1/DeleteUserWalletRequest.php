<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class DeleteUserWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        Log::info($this->route('user_wallet')->user_id . ' ' . $user->id);
        if ($this->route('user_wallet')->user_id != $user->id) {
            return false;
        }
        return $user != null && $user->tokenCan('delete');
    }

    public function rules(): array
    {
        Log::info('Hello');
        return [
            //
        ];
    }
}
