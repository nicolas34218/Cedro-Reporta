<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alterar a tabela reports para referenciar citizens ao invés de users
        // E adicionar secretário atribuído
        
        Schema::table('reports', function (Blueprint $table) {
            // Adiciona coluna secretary_id se não existir
            if (!Schema::hasColumn('reports', 'secretary_id')) {
                $table->foreignId('secretary_id')
                    ->nullable()
                    ->after('category')
                    ->constrained('secretaries')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'secretary_id')) {
                $table->dropForeignKey(['secretary_id']);
                $table->dropColumn('secretary_id');
            }
        });
    }
};
