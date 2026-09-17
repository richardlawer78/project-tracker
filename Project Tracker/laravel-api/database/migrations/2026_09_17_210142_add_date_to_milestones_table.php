<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('milestones', 'date')) {
            Schema::table('milestones', function (Blueprint $table) {
                $table->date('date')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        // The date column may have been created by the original
        // create_milestones_table migration, so don't remove it here.
    }
};