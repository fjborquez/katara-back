<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonHouseRequest extends FormRequest
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
        return [
            'name' => ['required', 'max:30', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜ]+(?:\s[a-zA-ZáéíóúÁÉÍÓÚüÜ]+)*$/'],
            'lastname' => ['required', 'max:30', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜ]+(?:\s[a-zA-ZáéíóúÁÉÍÓÚüÜ]+)*$/'],
            'date_of_birth' => ['required', 'date_format:d/m/Y', 'before:tomorrow'],
            'nutritional_profile' => ['required', 'array'],
        ];
    }
}
