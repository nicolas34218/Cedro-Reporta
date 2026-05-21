<?php

namespace App\Http\Requests;

use App\Constants\ReportConstants;
use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para criar nova secretária.
 * * Segue os mesmos critérios de validação de senha que o RegisterRequest.
 */
class CreateSecretaryRequest extends FormRequest
{
    /**
     * Determina se o usuário é autorizado a fazer este request.
     * Apenas admins podem criar secretárias.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    /**
     * Obtém as regras de validação que se aplicam ao request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Adicionado a regra unique na tabela secretaries para a coluna name
            'name' => 'required|string|max:255|unique:secretaries,name',
            'email' => 'required|string|email|max:255|unique:secretaries',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
            'category' => 'required|string|in:' . ReportConstants::getCategoriesValidation(),
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
            'name.required' => 'O nome da secretária é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode exceder 255 caracteres.',
            'name.unique' => 'Já existe uma secretaria cadastrada com este nome.', // Mensagem adicionada
            
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
            'category.required' => 'Selecione uma categoria para a secretária.',
            'category.in' => 'A categoria selecionada é inválida.',
        ];
    }
}