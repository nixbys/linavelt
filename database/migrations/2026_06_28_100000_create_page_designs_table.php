<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('Untitled Design');
            $table->string('status', 20)->default('draft');
            $table->json('project_data')->nullable();
            $table->longText('html')->nullable();
            $table->longText('css')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_designs');
    }
};
