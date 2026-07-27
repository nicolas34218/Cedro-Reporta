<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RespondReportShareRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // A autorização (apenas administradores da secretaria destino) 
        // será tratada isoladamente na Policy.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Verifica se a requisição está sendo feita para o endpoint de rejeição
        $isRejecting = str_contains($this->path(), 'rejeitar');

        return [
            'justification' => [
                $isRejecting ? 'required' : 'nullable',
                'string',
                'min:5'
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'justification.required' => 'A justificativa é obrigatória ao rejeitar um compartilhamento.',
            'justification.min'      => 'A justificativa deve conter pelo menos 5 caracteres.',
        ];
    }
}