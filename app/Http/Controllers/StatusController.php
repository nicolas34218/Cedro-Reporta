<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function edit(Report $report)
    {
        return view('status.edit', [
            'report' => $report,
        ]);
    }

    public function updateStatus(Request $request, Report $report) 
    {
        // Validação: status é obrigatório
        $validated = $request->validate([
            'status' => ['required', 'in:Pendente,Em Análise,Resolvida'],
        ], [
            'status.required' => 'Selecione um status para a denúncia.',
            'status.in' => 'Status inválido.',
        ]);

        try {
            // Atualizar status
            $report->update([
                'status' => $validated['status'],
                'status_updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\Log::info('Status da denúncia atualizado', [
                'report_id' => $report->id,
                'new_status' => $validated['status'],
            ]);

            return redirect()->route('secretary.classify-reports')
                ->with('success', "Denúncia #{$report->id} atualizada para {$validated['status']} com sucesso!");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao atualizar status da denúncia', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
             
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar status. Tente novamente.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar status da denúncia. Tente novamente.');
        }
    }   
}