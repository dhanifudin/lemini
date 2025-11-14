<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_style_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('learning_style');
            $table->string('detection_method')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->json('quiz_results')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'learning_style']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_style_preferences');
    }
};
