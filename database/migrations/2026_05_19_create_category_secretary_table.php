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
        Schema::create('category_secretary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
            $table->foreignId('secretary_id')
                ->references('id')
                ->on('secretaries')
                ->onDelete('cascade');
            $table->timestamps();
            
            // Garante que não há duplicatas
            $table->unique(['category_id', 'secretary_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_secretary');
    }
};