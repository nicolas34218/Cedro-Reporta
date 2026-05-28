<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Secretary; // Necessário para listar as secretarias
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controller para gerenciar categorias de denúncias.
 */
class CategoryController extends Controller
{
    /**
     * Exibe o formulário para criar uma nova categoria.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Busca as secretarias ativas para o dropdown
        $secretaries = Secretary::where('is_active', true)
            ->orderBy('name')
            ->get();

        // CAMINHO CORRIGIDO: Voltando para a view original!
        return view('category.create', [
            'categories' => $categories,
            'secretaries' => $secretaries,
        ]);
    }

    /**
     * Armazena uma nova categoria no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validações rigorosas adicionadas
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:5',
                'max:50',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/', // Apenas letras (incluindo acentos) e espaços
                Rule::unique('categories', 'name'),
            ],
            'description' => [
                'required',
                'string', 
                'min:10', 
                'max:255'
            ],
            'secretary_id' => [
                'required',
                'exists:secretaries,id'
            ]
        ], [
            'name.unique' => 'Esta categoria já existe no sistema.',
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.min' => 'O nome deve ter no mínimo 5 caracteres.',
            'name.max' => 'O nome deve ter no máximo 50 caracteres.',
            'name.regex' => 'O nome da categoria deve conter apenas letras e espaços.',
            'description.required' => 'A descrição é obrigatória.',
            'description.min' => 'A descrição deve ter no mínimo 10 caracteres.',
            'description.max' => 'A descrição deve ter no máximo 255 caracteres.',
            'secretary_id.required' => 'Você deve selecionar uma secretaria responsável.',
            'secretary_id.exists' => 'A secretaria selecionada é inválida.',
        ]);

        try {
            // Usa transação para garantir que, se der erro, NADA é salvo pela metade
            \Illuminate\Support\Facades\DB::beginTransaction();

            $category = Category::create([
                'name' => trim($validated['name']),
                'description' => trim($validated['description']),
                'is_active' => true,
                // SALVA O ID DA SECRETARIA DIRETAMENTE AQUI (Se for relação 1 para N)
                'secretary_id' => $validated['secretary_id'], 
            ]);

            // Se você REALMENTE usar uma tabela Pivot (Muitos-para-Muitos), descomente a linha abaixo e remova o secretary_id de cima:
            // $category->secretaries()->attach($validated['secretary_id']);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('admin.dashboard')
                ->with('success', "Categoria '{$category->name}' criada com sucesso!");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack(); // Desfaz a criação da categoria se der erro
            
            // Log do erro real para você saber o que falhou!
            \Illuminate\Support\Facades\Log::error('Erro real ao criar categoria: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao vincular categoria e secretaria. (Verifique os logs)');
        }
    }
}