<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('lessons_learned', function (Blueprint $table) { $table->id(); $table->foreignId('project_id')->constrained()->cascadeOnDelete(); $table->string('title'); $table->enum('category', ['process', 'technical', 'team', 'scope', 'quality']); $table->enum('impact', ['positive', 'negative']); $table->text('description')->nullable(); $table->date('date'); $table->timestamps(); }); }
    public function down(): void { Schema::dropIfExists('lessons_learned'); }
};
