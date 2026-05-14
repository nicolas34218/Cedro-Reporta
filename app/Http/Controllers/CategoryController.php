<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controller para gerenciar categorias de denúncias.
 * 
 * Responsabilidades:
 * - Criar novas categorias
 * - Validar nomes duplicados
 * - Listar categorias ativas
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
        return view('admin.categories.create');
    }

    /**
     * Armazena uma nova categoria no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validação com regra de unique
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name'),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.unique' => 'Esta categoria já existe no sistema.',
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
        ]);

        try {
            // Criar categoria
            $category = Category::create([
                'name' => trim($validated['name']),
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', "Categoria '{$category->name}' criada com sucesso!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar categoria. Tente novamente.');
        }
    }

    /**
     * Lista todas as categorias ativas.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveCategories()
    {
        return Category::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}

