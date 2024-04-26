<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'max:30', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜ]+(?:\s[a-zA-ZáéíóúÁÉÍÓÚüÜ]+)*$/'],
            'lastname' => ['required', 'max:30', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜ]+(?:\s[a-zA-ZáéíóúÁÉÍÓÚüÜ]+)*$/'],
            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:tomorrow'],
        ];

        if (!empty($this->get('email')) || !empty($this->get('password')))
        {
            $rules['email'] = ['required', 'email:rfc,dns'];
            $rules['password'] = ['required', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()];
        }

        return $rules;
    }
}
