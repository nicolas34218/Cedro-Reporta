<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE reports MODIFY status ENUM('Pendente', 'Em Análise', 'Resolvida', 'Fechada', 'Aberta') NOT NULL DEFAULT 'Pendente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE reports MODIFY status ENUM('Aberta', 'Em Análise', 'Resolvida', 'Fechada') NOT NULL DEFAULT 'Aberta'");
    }
};