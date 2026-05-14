<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate dados da tabela users antiga para as 3 novas tabelas, se existir
     */
    public function up(): void
    {
        // Apenas migra dados se a tabela users existir (para suportar upgrade de sistemas existentes)
        if (!Schema::hasTable('users')) {
            return;
        }

        // Buscar todos os usuários da tabela antiga
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'password' => $user->password,
                'is_active' => $user->is_active,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];

            if ($user->user_type === 'Cidadão') {
                // Migrar para citizens
                DB::table('citizens')->insert($data);
            } elseif ($user->user_type === 'Admin') {
                // Migrar para admins
                DB::table('admins')->insert($data);
            } elseif ($user->user_type === 'Secretário') {
                // Migrar para secretaries
                $data['category'] = $user->category;
                DB::table('secretaries')->insert($data);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Deletar dados migrados de volta (apenas se users existir)
        if (Schema::hasTable('users')) {
            DB::table('citizens')->truncate();
            DB::table('admins')->truncate();
            DB::table('secretaries')->truncate();
        }
    }
};
