<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('secretary_id')
                ->nullable()
                ->after('is_active')
                ->constrained('secretaries')
                ->nullOnDelete();
        });

        // Migra o vínculo existente (secretaries.category) para a nova FK.
        DB::table('categories')->select('id', 'name')->orderBy('id')->get()->each(function ($category) {
            $secretary = DB::table('secretaries')->where('category', $category->name)->first();

            if ($secretary) {
                DB::table('categories')->where('id', $category->id)->update(['secretary_id' => $secretary->id]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('secretary_id');
        });
    }
};
