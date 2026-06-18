<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Corrige denúncias criadas com secretary_id nulo por causa do bug em que
     * ReportController lia uma coluna inexistente (categories.secretary_id)
     * antes desta FK existir de fato.
     */
    public function up(): void
    {
        DB::table('reports')
            ->whereNull('secretary_id')
            ->select('id', 'category')
            ->get()
            ->each(function ($report) {
                $secretaryId = DB::table('categories')
                    ->where('name', $report->category)
                    ->value('secretary_id');

                if ($secretaryId) {
                    DB::table('reports')->where('id', $report->id)->update(['secretary_id' => $secretaryId]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backfill de dados não é reversível de forma segura.
    }
};
