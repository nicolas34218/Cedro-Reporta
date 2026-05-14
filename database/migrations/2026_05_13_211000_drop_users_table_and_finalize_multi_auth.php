<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove a tabela users antiga e consolidar a autenticação multi-modelo
     */
    public function up(): void
    {
        // Drop da tabela users antiga se existir (e todas as referências a ela)
        Schema::disableForeignKeyConstraints();
        
        if (Schema::hasTable('users')) {
            Schema::dropIfExists('users');
        }

        // Limpar também a tabela sessions e password_reset_tokens criadas pela migration original
        // Mas manter a estrutura de sessions intacta para as novas tabelas
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não faz rollback - esta migration é irreversível (remove dados antigos)
    }
};
