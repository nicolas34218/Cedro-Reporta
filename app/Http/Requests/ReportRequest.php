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
        return true;
    }

    /**
     * Obtém as regras de validação que se aplicam ao request.
     *
     * Critérios de Aceitação:
     * - O título, descrição, categoria, endereço e bairro são obrigatórios
     * - A descrição deve ter no mínimo 10 caracteres
     * - Imagens devem estar nos formatos PNG, JPG ou JPEG com máximo de 2MB
     * - Captcha deve ser preenchido para evitar bots
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:10|max:1000',
            'category' => 'required|string|exists:categories,name',
            'address_reference' => 'required|string|min:3|max:255',
            'district' => 'required|string|min:3|max:100',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'captcha_answer' => $this->isVisitorReport() ? 'required|numeric' : 'nullable|numeric',
            'anonymous' => 'nullable|boolean',
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
            'captcha_answer' => $this->input('captcha_answer') ? trim($this->input('captcha_answer')) : null,
        ]);
    }

    /**
     * Obtém as mensagens de validação customizadas.
     *
     * @return array<string, string>
     */
    private function isVisitorReport(): bool
    {
        return $this->routeIs('visitor.reports.store');
    }

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
            'category.exists' => 'A categoria selecionada é inválida.',
            'address_reference.required' => 'O endereço/referência é obrigatório.',
            'address_reference.min' => 'O endereço deve ter no mínimo 3 caracteres.',
            'address_reference.max' => 'O endereço não pode exceder 255 caracteres.',
            'district.required' => 'O bairro é obrigatório.',
            'district.min' => 'O bairro deve ter no mínimo 3 caracteres.',
            'district.max' => 'O bairro não pode exceder 100 caracteres.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve estar nos formatos PNG, JPG ou JPEG.',
            'image.max' => 'A imagem não pode exceder 2MB.',
            'captcha_answer.required' => 'A validação anti-bot é obrigatória.',
            'captcha_answer.numeric' => 'A resposta do Captcha deve ser um número válido.',
        ];
    }
}
