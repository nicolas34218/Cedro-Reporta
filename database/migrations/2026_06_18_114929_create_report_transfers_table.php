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
        Schema::create('report_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('from_secretary_id')->constrained('secretaries')->cascadeOnDelete();
            $table->foreignId('to_secretary_id')->constrained('secretaries')->cascadeOnDelete();
            $table->text('justification');
            $table->string('status')->default('Pendente');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_transfers');
    }
};
