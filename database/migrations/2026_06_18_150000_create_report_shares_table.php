<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('from_secretary_id')->constrained('secretaries')->cascadeOnDelete();
            $table->foreignId('to_secretary_id')->constrained('secretaries')->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->timestamp('shared_at')->nullable();
            $table->timestamps();

            $table->unique(['report_id', 'to_secretary_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_shares');
    }
};