<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder para popular o banco com dados de teste de denúncias.
 *
 * Útil para testar as funcionalidades de visualizar e acompanhar denúncias.
 *
 * Para executar:
 * php artisan db:seed --class=ReportSeeder
 */
class ReportSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Cria um usuário teste e 5 denúncias com diferentes status
     */
    public function run(): void
    {
        // Cria um usuário de teste se não existir
        $user = User::firstOrCreate(
            ['email' => 'teste@cedro.local'],
            [
                'name' => 'Usuário Teste',
                'password' => bcrypt('123456'),
                'user_type' => 'Cidadão',
                'is_active' => true,
            ]
        );

        // Denúncia 1: Pendente (mais recente)
        Report::create([
            'user_id' => $user->id,
            'title' => 'Buraco grande na Rua Principal',
            'description' => 'Existe um buraco muito grande na Rua Principal que está causando acidentes. Já reportei várias vezes mas nada foi feito. A prefeitura precisa fazer o reparo urgente.',
            'category' => 'Buracos',
            'status' => 'Pendente',
            'location' => 'Rua Principal, 123 - Centro',
        ]);

        // Denúncia 2: Em Análise
        Report::create([
            'user_id' => $user->id,
            'title' => 'Falta de iluminação pública',
            'description' => 'As ruas do bairro ficam muito escuras à noite. Vários postes de iluminação estão queimados e prejudicam a segurança dos moradores. Solicito urgência no reparo.',
            'category' => 'Iluminação',
            'status' => 'Em Análise',
            'location' => 'Bairro São José',
        ]);

        // Denúncia 3: Resolvida
        Report::create([
            'user_id' => $user->id,
            'title' => 'Lixo acumulado na calçada',
            'description' => 'Havia muito lixo acumulado na calçada do pátio escolar. Registrei a reclamação e gostaríamos de saber quando será retirado.',
            'category' => 'Lixo',
            'status' => 'Resolvida',
            'location' => 'Rua da Escola, 45 - Pátio Escolar',
        ]);

        // Denúncia 4: Fechada
        Report::create([
            'user_id' => $user->id,
            'title' => 'Calçada danificada',
            'description' => 'A calçada em frente à minha casa está toda quebrada e oferece perigo. Realizei a denúncia solicitando reparo de urgência.',
            'category' => 'Segurança',
            'status' => 'Fechada',
            'location' => 'Avenida Brasil, 789 - Bairro das Flores',
        ]);

        // Denúncia 5: Pendente (mais antiga)
        Report::create([
            'user_id' => $user->id,
            'title' => 'Problema de segurança na praça',
            'description' => 'A praça central ficou insegura à noite. Solicito maior policiamento e iluminação adequada. As crianças não podem mais brincar depois de escurecer.',
            'category' => 'Segurança',
            'status' => 'Pendente',
            'location' => 'Praça Central - Downtown',
        ]);

        echo "✅ 5 denúncias de teste foram criadas para o usuário: teste@cedro.local\n";
        echo "🔓 Senha: 123456\n\n";
        echo "Você pode usar estes dados para testar:\n";
        echo "  1. Visualizar lista de denúncias (ordenadas por mais recentes)\n";
        echo "  2. Ver detalhes de cada denúncia\n";
        echo "  3. Acompanhar status com timeline visual\n";
    }
}
