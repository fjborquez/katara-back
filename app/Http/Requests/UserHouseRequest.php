<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserHouseRequest extends FormRequest
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
            'description' => ['required', 'max:30'],
            'city_id' => ['required'],
            'is_default' => [],
            'house_id' => [],
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages(): array
    {
        return [
            'city_id.required' => 'The city field is required.',
        ];
    }
}
