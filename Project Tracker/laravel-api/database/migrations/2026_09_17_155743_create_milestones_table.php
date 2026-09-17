<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('milestones')) {
            if (! Schema::hasColumn('milestones', 'description')) {
                Schema::table('milestones', function (Blueprint $table) {
                    $table->text('description')->nullable();
                });
            }

            if (! Schema::hasColumn('milestones', 'due_date')) {
                Schema::table('milestones', function (Blueprint $table) {
                    $table->date('due_date')->nullable();
                });
            }

            return;
        }

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'in-progress', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
