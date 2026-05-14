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
            // Campo para armazenar qual secretária é responsável pela denúncia
            $table->foreignId('assigned_secretary_id')->nullable()->references('id')->on('secretaries')->after('category');
            
            // Índice para melhorar queries que filtram por secretária
            $table->index('assigned_secretary_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeignKey(['assigned_secretary_id']);
            $table->dropIndex(['assigned_secretary_id']);
            $table->dropColumn('assigned_secretary_id');
        });
    }
};
