<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Adiciona coluna de prioridade (Baixa, Média, Alta, Urgente)
            $table->enum('priority', ['Baixa', 'Média', 'Alta', 'Urgente'])
                ->nullable()
                ->default(null)
                ->comment('Nível de prioridade da denúncia');

            // Adiciona coluna para justificativa (obrigatória para Urgente)
            $table->text('priority_justification')
                ->nullable()
                ->comment('Justificativa para denúncias urgentes');

            // Adiciona timestamp de quando a prioridade foi atribuída
            $table->timestamp('priority_assigned_at')
                ->nullable()
                ->comment('Data em que a prioridade foi atribuída');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['priority', 'priority_justification', 'priority_assigned_at']);
        });
    }
};
