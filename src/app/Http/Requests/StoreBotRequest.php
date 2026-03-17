<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBotRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'token' => [
                'required',
                'string',
                Rule::unique('telegraph_bots', 'token'),
                'regex:/^[0-9]{8,10}:[a-zA-Z0-9_-]{35}$/', // Формат токена Telegram
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Название бота обязательно',
            'name.min' => 'Название должно быть не менее 3 символов',
            'name.max' => 'Название не должно превышать 255 символов',
            'token.required' => 'Токен бота обязателен',
            'token.unique' => 'Этот токен уже используется',
            'token.regex' => 'Неверный формат токена Telegram',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'token' => trim($this->token),
            'name' => trim($this->name),
        ]);
    }
}
