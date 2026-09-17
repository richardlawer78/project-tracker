<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('milestones', 'date')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            Schema::table('milestones', function (Blueprint $table) {
                $table->date('date')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->date('date')->nullable(false)->change();
        });
    }
};
