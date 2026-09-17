<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('milestones', 'due_date')) {
            Schema::table('milestones', function (Blueprint $table) {
                $table->date('due_date')->nullable();
            });
        }

        if (Schema::hasColumn('milestones', 'date')) {
            DB::statement('UPDATE milestones SET due_date = date WHERE due_date IS NULL');
        }
    }

    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
