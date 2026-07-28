<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_shares', function (Blueprint $table) {

            $table->string('status')
                  ->default('pending')
                  ->after('message');

            $table->text('response')
                  ->nullable()
                  ->after('status');

            $table->timestamp('responded_at')
                  ->nullable()
                  ->after('response');

        });
    }

    public function down(): void
    {
        Schema::table('report_shares', function (Blueprint $table) {

            $table->dropColumn([
                'status',
                'response',
                'responded_at',
            ]);

        });
    }
};