<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('module_generation_status', 20)->nullable()->after('onboarding_completed_at');
            $table->timestamp('module_generation_started_at')->nullable()->after('module_generation_status');
            $table->timestamp('module_generation_completed_at')->nullable()->after('module_generation_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'module_generation_status',
                'module_generation_started_at',
                'module_generation_completed_at',
            ]);
        });
    }
};
