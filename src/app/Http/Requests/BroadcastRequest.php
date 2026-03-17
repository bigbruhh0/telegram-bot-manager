<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastRequest extends FormRequest
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
            'message' => [
                'required',
                'string',
                'max:4096',
                'min:1',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'message.required' => 'Сообщение обязательно',
            'message.max' => 'Сообщение не должно превышать 4096 символов',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'message' => trim($this->message),
        ]);
    }
}
