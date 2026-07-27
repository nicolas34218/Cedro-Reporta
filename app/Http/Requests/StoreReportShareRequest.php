<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Report;
use App\Models\ReportShare;

class StoreReportShareRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // A autorização baseada no usuário logado e na sua secretaria 
        // será feita preferencialmente via Policy ou Controller.
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Resgata o ID ou a model da denúncia vindo da rota (ex: /api/reports/{id}/share)
        $reportId = $this->route('id') ?? $this->route('report');

        return [
            'destination_secretary_id' => [
                'required',
                'integer',
                'exists:secretaries,id',
                function ($attribute, $value, $fail) use ($reportId) {
                    if (!$reportId) return;

                    $report = Report::find($reportId);
                    if (!$report) return;

                    // 1. Impedir compartilhar para si mesmo
                    if ($report->secretary_id == $value) {
                        $fail('A denúncia já pertence a esta secretaria e não pode ser compartilhada com ela mesma.');
                    }

                    // 2. Impedir compartilhamentos pendentes duplicados para a mesma secretaria
                    $hasPendingShare = ReportShare::where('report_id', $report->id)
                        ->where('destination_secretary_id', $value)
                        ->where('status', 'PENDENTE') // Ajuste caso utilize um Enum próprio do projeto
                        ->exists();

                    if ($hasPendingShare) {
                        $fail('Já existe um compartilhamento pendente para esta secretaria.');
                    }
                },
            ],
            'justification' => ['required', 'string', 'min:5'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'destination_secretary_id.required' => 'A secretaria de destino é obrigatória.',
            'destination_secretary_id.exists'   => 'A secretaria de destino selecionada não existe.',
            'justification.required'            => 'A justificativa é obrigatória para o compartilhamento.',
        ];
    }
}