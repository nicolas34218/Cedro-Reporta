<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\Admin;
use App\Models\Secretary;
use App\Models\Report;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria categorias
        $categories = [
            ['name' => 'Iluminação', 'description' => 'Problemas de iluminação pública'],
            ['name' => 'Buracos', 'description' => 'Buracos em ruas e avenidas'],
            ['name' => 'Lixo', 'description' => 'Problemas com lixo e limpeza'],
            ['name' => 'Segurança', 'description' => 'Problemas de segurança pública'],
            ['name' => 'Saúde', 'description' => 'Problemas relacionados à saúde'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'description' => $cat['description'],
                'is_active' => true,
            ]);
        }

        // Cria usuário Admin
        Admin::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@cedroreporta.com',
            'password' => bcrypt('admin123'),
            'is_active' => true,
        ]);

        // Cria secretárias, uma para cada categoria
        $slugs = [
            'Iluminação' => 'iluminacao',
            'Buracos' => 'buracos',
            'Lixo' => 'lixo',
            'Segurança' => 'seguranca',
            'Saúde' => 'saude',
        ];

        foreach ($categories as $cat) {
            $slug = $slugs[$cat['name']] ?? strtolower(str_replace(' ', '', $cat['name']));
            
            Secretary::create([
                'name' => 'Secretaria ' . $cat['name'],
                'email' => 'secretaria.' . $slug . '@cedroreporta.com',
                'password' => bcrypt('secretary123'),
                'category' => $cat['name'], // Associa a secretária à categoria
                'is_active' => true,
            ]);
        }

        // Cria 5 usuários cidadãos para teste
        $citizens = Citizen::factory(5)->create([
            'is_active' => true,
        ]);

        // Cria denúncias de teste
        $categoryNames = array_column($categories, 'name');
        $statuses = ['Aberta', 'Em Análise', 'Resolvida', 'Fechada'];

        foreach ($citizens as $citizen) {
            for ($i = 0; $i < 3; $i++) {
                $categoryName = fake()->randomElement($categoryNames);
                
                // Busca a secretária responsável pela categoria
                $secretary = Secretary::where('category', $categoryName)->first();
                
                Report::create([
                    'user_id' => $citizen->id,
                    'title' => fake()->sentence(4),
                    'description' => fake()->text(200),
                    'category' => $categoryName,
                    'status' => fake()->randomElement($statuses),
                    'location' => fake()->address(),
                    'image_path' => null,
                    'secretary_id' => $secretary?->id, // Atribui a secretária responsável
                ]);
            }
        }
    }
}
