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
        // Nunca chegou a ser usada para vincular categorias e secretarias.
        Schema::dropIfExists('category_secretary');

        // Substituída por categories.secretary_id (uma categoria pertence a uma única secretaria).
        Schema::table('secretaries', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('secretaries', function (Blueprint $table) {
            $table->string('category')->nullable();
        });

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

            $table->unique(['category_id', 'secretary_id']);
        });
    }
};
