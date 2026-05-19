<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Secretary;
use Illuminate\Database\Seeder;

class CategorySecretarySeeder extends Seeder
{
    /**
     * Executa o seed das associações entre categorias e secretárias.
     *
     * @return void
     */
    public function run(): void
    {
        // Busca todas as secretárias ativas
        $secretaries = Secretary::where('is_active', true)->get();

        // Se houver secretárias, associa com as categorias
        foreach ($secretaries as $secretary) {
            // Se a secretária tem um campo 'category', usa esse para associar
            if (!empty($secretary->category)) {
                $category = Category::where('name', $secretary->category)->first();
                
                if ($category) {
                    // Associa a secretária à categoria (se já não estiver associada)
                    $category->secretaries()->syncWithoutDetaching([$secretary->id]);
                    
                    echo "✓ Secretária '{$secretary->name}' associada à categoria '{$category->name}'\n";
                }
            }
        }

        // Se quiser associar manualmente, também pode fazer assim:
        // Exemplo: Associar todas as secretárias a TODAS as categorias
        // Descomente as linhas abaixo se quiser isso:
        
        /*
        $categories = Category::all();
        foreach ($secretaries as $secretary) {
            foreach ($categories as $category) {
                $category->secretaries()->syncWithoutDetaching([$secretary->id]);
            }
        }
        */
    }
}