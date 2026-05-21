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
            // Criar categoria
            $category = Category::create([
                'name' => trim($validated['name']),
                'description' => trim($validated['description']),
                'is_active' => true,
            ]);

            // Vincula a secretaria escolhida à categoria criada
            $category->secretaries()->attach($validated['secretary_id']);

            return redirect()->route('admin.dashboard')
                ->with('success', "Categoria '{$category->name}' criada com sucesso!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar categoria. Tente novamente.');
        }
    }

}