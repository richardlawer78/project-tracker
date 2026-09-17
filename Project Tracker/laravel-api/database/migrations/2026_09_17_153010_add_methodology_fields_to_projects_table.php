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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('methodology')->nullable();
            $table->string('sprint_duration')->nullable();
            $table->text('sprint_goal')->nullable();
            $table->unsignedInteger('velocity')->nullable();
            $table->text('phases')->nullable();
            $table->text('milestones_text')->nullable();
            $table->text('deliverables')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['methodology', 'sprint_duration', 'sprint_goal', 'velocity', 'phases', 'milestones_text', 'deliverables']);
        });
    }
};
