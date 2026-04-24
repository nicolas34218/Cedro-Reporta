<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@cedroreporta.com',
            'password' => bcrypt('senha123'),
            'user_type' => 'Admin',
            'is_active' => true,
        ]);

        // Cria usuário Secretário
        User::create([
            'name' => 'Secretário Prefeitura',
            'email' => 'secretario@cedroreporta.com',
            'password' => bcrypt('senha123'),
            'user_type' => 'Secretário',
            'is_active' => true,
        ]);

        // Cria 5 usuários cidadãos para teste
        $citizens = User::factory(5)->create([
            'user_type' => 'Cidadão',
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
