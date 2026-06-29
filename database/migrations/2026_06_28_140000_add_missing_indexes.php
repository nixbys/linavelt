<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('module_generation_status');
            $table->index('is_admin');
        });

        Schema::table('builder_revisions', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
        });

        Schema::table('page_designs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['module_generation_status']);
            $table->dropIndex(['is_admin']);
        });

        Schema::table('builder_revisions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('page_designs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });
    }
};
