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
        Schema::table('project_interests', function (Blueprint $table) {
            $table->foreignId('project_id')
                ->after('id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->string('name', 100)->after('project_id');
            $table->string('email')->after('name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('company', 150)->nullable()->after('phone');
            $table->text('message')->nullable()->after('company');
            $table->string('status', 30)->default('new')->after('message');

            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_interests', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropIndex('project_interests_email_index');
            $table->dropIndex('project_interests_status_index');
            $table->dropColumn([
                'project_id',
                'name',
                'email',
                'phone',
                'company',
                'message',
                'status',
            ]);
        });
    }
};
