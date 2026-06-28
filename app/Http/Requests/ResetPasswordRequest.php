<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para redefinição de senha.
 *
 * A nova senha segue exatamente as mesmas regras aplicadas na criação
 * da conta (ver RegisterRequest): mínimo de 6 caracteres e confirmação.
 */
class ResetPasswordRequest extends FormRequest
{
    /**
     * Determina se o usuário é autorizado a fazer este request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtém as regras de validação que se aplicam ao request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ];
    }

    /**
     * Obtém as mensagens de validação customizadas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'current_password.required' => 'A senha atual é obrigatória.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.min' => 'A nova senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'A confirmação da nova senha não corresponde.',
            'password_confirmation.required' => 'A confirmação da nova senha é obrigatória.',
        ];
    }
}
