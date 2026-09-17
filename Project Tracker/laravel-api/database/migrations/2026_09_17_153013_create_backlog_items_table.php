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
        Schema::create('backlog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['epic', 'feature', 'story']);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->unsignedInteger('points')->default(0);
            $table->enum('status', ['backlog', 'ready', 'in-progress'])->default('backlog');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlog_items');
    }
};
