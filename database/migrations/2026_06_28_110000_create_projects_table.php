<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 50)->default('website');
            $table->string('language', 50)->nullable();
            $table->string('framework', 100)->nullable();
            $table->json('integrations')->nullable();
            $table->json('stack_config')->nullable();
            $table->string('status', 20)->default('draft');
            $table->json('project_data')->nullable();
            $table->longText('html')->nullable();
            $table->text('css')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
