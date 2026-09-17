<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('risks', 'description')) {
            Schema::table('risks', function (Blueprint $table) {
                $table->text('description')->nullable();
            });
        }

        if (! Schema::hasColumn('risks', 'mitigation')) {
            Schema::table('risks', function (Blueprint $table) {
                $table->text('mitigation')->nullable();
            });
        }

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // The earlier migration already created this table.  Upgrade it in
        // place so existing risk records are retained.
        Schema::table('risks', function (Blueprint $table) {
            $table->string('category')->nullable()->change();
            $table->foreignId('owner_id')->nullable()->change();
        });

        DB::table('risks')->where('status', 'mitigating')->update(['status' => 'mitigated']);

        // Laravel's PostgreSQL enum columns are CHECK constraints. Remove
        // the old constraint after data normalization, then add the status
        // values used by the current model and controller.
        DB::unprepared(<<<'SQL'
            DO $$
            DECLARE constraint_name text;
            BEGIN
                SELECT conname INTO constraint_name
                FROM pg_constraint
                WHERE conrelid = 'risks'::regclass
                  AND contype = 'c'
                  AND pg_get_constraintdef(oid) LIKE '%status%'
                LIMIT 1;

                IF constraint_name IS NOT NULL THEN
                    EXECUTE format('ALTER TABLE risks DROP CONSTRAINT %I', constraint_name);
                END IF;
            END $$;
        SQL);
        DB::statement("ALTER TABLE risks ALTER COLUMN status SET DEFAULT 'open'");
        DB::statement("ALTER TABLE risks ADD CONSTRAINT risks_status_check CHECK (status IN ('open', 'mitigated', 'closed'))");
    }

    public function down(): void
    {
        // Intentionally non-destructive: this migration upgrades a populated
        // legacy table and must not remove its records on rollback.
    }
};
