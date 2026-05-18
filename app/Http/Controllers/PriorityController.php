<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller para gerenciar a classificação de prioridade de denúncias.
 * 
 * Permite que secretários classifiquem denúncias com níveis de prioridade:
 * - Baixa
 * - Média
 * - Alta
 * - Urgente (requer justificativa)
 */
class PriorityController extends Controller
{
    /**
     * Exibe o formulário de classificação de denúncia.
     * Apenas secretários podem classificar.
     *
     * @param Report $report
     * @return \Illuminate\View\View
     */
    public function edit(Report $report)
    {
        return view('priority.edit', [
            'report' => $report,
        ]);
    }

    /**
     * Atualiza a prioridade da denúncia.
     * Valida e salva a classificação no banco de dados.
     *
     * @param Request $request
     * @param Report $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Report $report)
    {
        // Validação: prioridade é obrigatória
        $validated = $request->validate([
            'priority' => ['required', 'in:Baixa,Média,Alta,Urgente'],
            'priority_justification' => ['nullable', 'string', 'max:500'],
        ], [
            'priority.required' => 'Selecione um nível de prioridade.',
            'priority.in' => 'Nível de prioridade inválido.',
            'priority_justification.max' => 'A justificativa não pode ter mais de 500 caracteres.',
        ]);

        // Se for Urgente, validar que justificativa foi fornecida
        if ($validated['priority'] === 'Urgente' && empty($validated['priority_justification'])) {
            return back()
                ->withInput()
                ->withErrors(['priority_justification' => 'A justificativa é obrigatória para denúncias urgentes.']);
        }

        try {
            // Atualizar prioridade
            $report->update([
                'priority' => $validated['priority'],
                'priority_justification' => $validated['priority_justification'] ?? null,
                'priority_assigned_at' => now(),
            ]);

            return redirect()->route('admin.reports')
                ->with('success', "Denúncia #{$report->id} classificada como {$validated['priority']} com sucesso!");

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao classificar denúncia. Tente novamente.');
        }
    }
}
