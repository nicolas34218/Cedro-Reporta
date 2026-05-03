<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para denúncias.
 *
 * Valida a criação e atualização de denúncias, garantindo que todos os
 * critérios de aceitação sejam cumpridos.
 */
class ReportRequest extends FormRequest
{
    /**
     * Determina se o usuário é autorizado a fazer este request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Obtém as regras de validação que se aplicam ao request.
     *
     * Critérios de Aceitação:
     * - A descrição deve ter no mínimo 10 caracteres
     * - Todas as informações obrigatórias devem estar preenchidas
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:10|max:1000',
            'category' => 'required|string|in:Iluminação,Buracos,Lixo,Segurança,Outros',
            'address_reference' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:100',
        ];
    }

    /**
     * Sanitiza os dados de entrada antes da validação.
     * Previne possíveis ataques XSS e injeção de dados.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->input('title') ? trim($this->input('title')) : null,
            'description' => $this->input('description') ? trim($this->input('description')) : null,
            'category' => $this->input('category') ? trim($this->input('category')) : null,
            'address_reference' => $this->input('address_reference') ? trim($this->input('address_reference')) : null,
            'district' => $this->input('district') ? trim($this->input('district')) : null,
        ]);
    }

    /**
     * Obtém as mensagens de validação customizadas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'O título da denúncia é obrigatório.',
            'title.min' => 'O título deve ter no mínimo 5 caracteres.',
            'title.max' => 'O título não pode exceder 255 caracteres.',
            'description.required' => 'A descrição é obrigatória.',
            'description.min' => 'A descrição deve ter no mínimo 10 caracteres.',
            'description.max' => 'A descrição não pode exceder 1000 caracteres.',
            'category.required' => 'A categoria é obrigatória.',
            'category.in' => 'A categoria selecionada é inválida.',
            'address_reference.max' => 'A referência de endereço não pode exceder 255 caracteres.',
            'district.max' => 'O bairro não pode exceder 100 caracteres.',
        ];
    }
}
