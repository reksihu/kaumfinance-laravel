<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Validation\Rules\Email;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if ($this->route('user')->id != $user->id) {
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
                'name' => ['required', 'string'],
                'email' => ['required', 'string'],
                'report_date_period' => ['required', 'numeric']
            ];
        } else {
            return [
                'name' => ['sometimes', 'required', 'string'],
                'email' => ['sometimes', 'required', 'string'],
                'report_date_period' => ['sometimes', 'required', 'numeric']
            ];
        }
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'report_date_period' => $this->reportDatePeriod
        ]);
    }
}
