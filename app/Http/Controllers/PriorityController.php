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
     * Retorna JSON para requisições AJAX ou redireciona para requisições normais.
     *
     * @param Request $request
     * @param Report $report
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Report $report)
    {
        // Validação: prioridade é obrigatória
        $validated = $request->validate([
            'priority' => ['required', 'in:Baixa,Média,Alta'],
        ], [
            'priority.required' => 'Selecione um nível de prioridade.',
            'priority.in' => 'Nível de prioridade inválido.',
        ]);

        try {
            // Atualizar prioridade
            $report->update([
                'priority' => $validated['priority'],
                'priority_assigned_at' => now(),
            ]);

            \Illuminate\Support\Facades\Log::info('Prioridade da denúncia atualizada', [
                'report_id' => $report->id,
                'new_priority' => $validated['priority'],
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Denúncia #{$report->id} classificada como {$validated['priority']} com sucesso!",
                    'priority' => $report->priority
                ], 200);
            }

            return redirect()->route('secretary.classify-reports')
                ->with('success', "Denúncia #{$report->id} classificada como {$validated['priority']} com sucesso!");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao classificar denúncia', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao classificar denúncia. Tente novamente.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Erro ao classificar denúncia. Tente novamente.');
        }
    }
}
