<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\Admin;
use App\Models\Secretary;
use App\Models\Report;
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
        // Cria usuário Admin
        Admin::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@cedroreporta.com',
            'password' => bcrypt('admin123'),
            'is_active' => true,
        ]);

        // Cria usuário Secretário
        Secretary::create([
            'name' => 'Secretário Prefeitura',
            'email' => 'secretario@cedroreporta.com',
            'password' => bcrypt('secretary123'),
            'category' => null,
            'is_active' => true,
        ]);

        // Cria 5 usuários cidadãos para teste
        $citizens = Citizen::factory(5)->create([
            'is_active' => true,
        ]);

        // Cria denúncias de teste
        $categories = ['Infraestrutura', 'Segurança', 'Limpeza', 'Saúde', 'Educação'];
        $statuses = ['Aberta', 'Em Análise', 'Resolvida', 'Fechada'];

        foreach ($citizens as $citizen) {
            for ($i = 0; $i < 3; $i++) {
                Report::create([
                    'user_id' => $citizen->id,
                    'title' => fake()->sentence(4),
                    'description' => fake()->text(200),
                    'category' => fake()->randomElement($categories),
                    'status' => fake()->randomElement($statuses),
                    'location' => fake()->address(),
                    'image_path' => null,
                ]);
            }
        }
    }
}
