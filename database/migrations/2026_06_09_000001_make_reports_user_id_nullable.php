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
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            DB::statement("CREATE TABLE reports_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NULL,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                category TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'Pendente',
                location TEXT NULL,
                image_path TEXT NULL,
                secretary_id INTEGER NULL,
                priority TEXT NULL,
                priority_justification TEXT NULL,
                priority_assigned_at TEXT NULL,
                created_at TEXT NULL,
                updated_at TEXT NULL,
                FOREIGN KEY(user_id) REFERENCES citizens(id) ON DELETE CASCADE,
                FOREIGN KEY(secretary_id) REFERENCES secretaries(id) ON DELETE SET NULL
            );");

            DB::statement("INSERT INTO reports_temp (id, user_id, title, description, category, status, location, image_path, secretary_id, priority, priority_justification, priority_assigned_at, created_at, updated_at)
                SELECT id, user_id, title, description, category, status, location, image_path, secretary_id, priority, priority_justification, priority_assigned_at, created_at, updated_at
                FROM reports;");

            DB::statement('DROP TABLE reports;');
            DB::statement('ALTER TABLE reports_temp RENAME TO reports;');
            DB::statement('PRAGMA foreign_keys=ON;');

            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE reports DROP FOREIGN KEY reports_user_id_foreign');
            DB::statement('ALTER TABLE reports MODIFY user_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE reports ADD CONSTRAINT reports_user_id_foreign FOREIGN KEY (user_id) REFERENCES citizens(id) ON DELETE CASCADE');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE reports ALTER COLUMN user_id DROP NOT NULL');

            return;
        }

        if ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE reports DROP CONSTRAINT reports_user_id_foreign');
            DB::statement('ALTER TABLE reports ALTER COLUMN user_id BIGINT NULL');
            DB::statement('ALTER TABLE reports ADD CONSTRAINT reports_user_id_foreign FOREIGN KEY (user_id) REFERENCES citizens(id) ON DELETE CASCADE');

            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            DB::statement("CREATE TABLE reports_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                category TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'Pendente',
                location TEXT NULL,
                image_path TEXT NULL,
                secretary_id INTEGER NULL,
                priority TEXT NULL,
                priority_justification TEXT NULL,
                priority_assigned_at TEXT NULL,
                created_at TEXT NULL,
                updated_at TEXT NULL,
                FOREIGN KEY(user_id) REFERENCES citizens(id) ON DELETE CASCADE,
                FOREIGN KEY(secretary_id) REFERENCES secretaries(id) ON DELETE SET NULL
            );");

            DB::statement("INSERT INTO reports_temp (id, user_id, title, description, category, status, location, image_path, secretary_id, priority, priority_justification, priority_assigned_at, created_at, updated_at)
                SELECT id, user_id, title, description, category, status, location, image_path, secretary_id, priority, priority_justification, priority_assigned_at, created_at, updated_at
                FROM reports;");

            DB::statement('DROP TABLE reports;');
            DB::statement('ALTER TABLE reports_temp RENAME TO reports;');
            DB::statement('PRAGMA foreign_keys=ON;');

            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE reports DROP FOREIGN KEY reports_user_id_foreign');
            DB::statement('ALTER TABLE reports MODIFY user_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE reports ADD CONSTRAINT reports_user_id_foreign FOREIGN KEY (user_id) REFERENCES citizens(id) ON DELETE CASCADE');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE reports ALTER COLUMN user_id SET NOT NULL');

            return;
        }

        if ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE reports DROP CONSTRAINT reports_user_id_foreign');
            DB::statement('ALTER TABLE reports ALTER COLUMN user_id BIGINT NOT NULL');
            DB::statement('ALTER TABLE reports ADD CONSTRAINT reports_user_id_foreign FOREIGN KEY (user_id) REFERENCES citizens(id) ON DELETE CASCADE');

            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('user_id')->change();
        });
    }
};
