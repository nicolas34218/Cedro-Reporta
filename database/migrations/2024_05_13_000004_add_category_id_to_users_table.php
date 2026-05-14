<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Esta migration foi depreciada - o campo category agora fica na tabela secretaries.
     * Mantida apenas para referência histórica.
     */
    public function up(): void
    {
        // Migration vazia - category agora é parte da tabela secretaries
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback vazio
    }
};
