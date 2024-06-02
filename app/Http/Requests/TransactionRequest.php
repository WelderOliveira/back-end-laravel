<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransactionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', 'numeric', 'min:0.01'],
            'payer' => ['required', 'integer', 'exists:users,id'],
            'payee' => ['required', 'integer', 'exists:users,id', 'different:payer'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'value.required' => 'O valor é obrigatório.',
            'value.numeric' => 'O valor deve ser um número.',
            'value.min' => 'O valor deve ser maior que 0.',
            'payer.required' => 'O pagador é obrigatório.',
            'payer.integer' => 'O pagador deve ser um ID de usuário válido.',
            'payer.exists' => 'O pagador deve ser um usuário existente.',
            'payee.required' => 'O recebedor é obrigatório.',
            'payee.integer' => 'O recebedor deve ser um ID de usuário válido.',
            'payee.exists' => 'O recebedor deve ser um usuário existente.',
            'payee.different' => 'O recebedor deve ser diferente do pagador.',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'data' => $validator->errors(),
            'message' => 'Validation errors'
        ]));
    }
}
