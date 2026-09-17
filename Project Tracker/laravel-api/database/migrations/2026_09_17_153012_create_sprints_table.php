<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('sprints')) {
            Schema::table('sprints', function (Blueprint $table) {
                if (! Schema::hasColumn('sprints', 'total_points')) {
                    $table->unsignedInteger('total_points')->default(0);
                }
                if (! Schema::hasColumn('sprints', 'completed_points')) {
                    $table->unsignedInteger('completed_points')->default(0);
                }
            });

            return;
        }

        Schema::create('sprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('goal')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planned', 'active', 'completed'])->default('planned');
            $table->unsignedInteger('total_points')->default(0);
            $table->unsignedInteger('completed_points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprints');
    }
};
