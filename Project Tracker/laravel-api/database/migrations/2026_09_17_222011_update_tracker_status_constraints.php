<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $this->replaceStatusConstraint(
            'milestones',
            ['upcoming', 'pending', 'in-progress', 'completed'],
        );

        $this->replaceStatusConstraint(
            'risks',
            ['open', 'mitigating', 'mitigated', 'closed'],
        );
    }

    public function down(): void
    {
        // This forward compatibility migration intentionally preserves existing status values.
    }

    /**
     * @param  array<int, string>  $statuses
     */
    private function replaceStatusConstraint(string $table, array $statuses): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $dropConstraint = str_replace('{table}', $table, <<<'SQL'
            DO $$
            DECLARE constraint_name text;
            BEGIN
                SELECT conname INTO constraint_name
                FROM pg_constraint
                WHERE conrelid = '{table}'::regclass
                  AND contype = 'c'
                  AND pg_get_constraintdef(oid) LIKE '%status%'
                LIMIT 1;

                IF constraint_name IS NOT NULL THEN
                    EXECUTE format('ALTER TABLE {table} DROP CONSTRAINT %I', constraint_name);
                END IF;
            END $$;
        SQL);

        DB::unprepared($dropConstraint);

        $allowedStatuses = implode(', ', array_map(
            static fn (string $status): string => "'{$status}'",
            $statuses,
        ));

        DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_status_check CHECK (status IN ({$allowedStatuses}))");
    }
};
