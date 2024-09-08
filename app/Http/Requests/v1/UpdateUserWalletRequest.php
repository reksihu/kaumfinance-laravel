<?php 

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateUserWalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return true;
        $user = $this->user();
        // Log::info($this->route('user_wallet')->id);
        if ($this->route('user_wallet')->user_id != $this->user()->id) {
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
        $userId = $this->user()->id;
        $method = $this->method();
        return [
            'name' => ['required', 'string', 'unique:user_wallets,name,' . $userId],
            'messages' => [
                'name.unique' => 'A user with this name already exists for your account.',
            ],
        ];
    }
}