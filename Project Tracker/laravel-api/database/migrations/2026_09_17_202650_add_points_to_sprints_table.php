<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sprints', function (Blueprint $table) {
            if (! Schema::hasColumn('sprints', 'total_points')) {
                $table->unsignedInteger('total_points')->default(0);
            }

            if (! Schema::hasColumn('sprints', 'completed_points')) {
                $table->unsignedInteger('completed_points')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('sprints', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('sprints', 'completed_points')) {
                $columns[] = 'completed_points';
            }

            // total_points may have been created by the original
            // create_sprints_table migration, so don't remove it here.
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};