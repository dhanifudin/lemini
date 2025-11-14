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
        Schema::create('learning_style_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('learning_style', ['visual', 'auditory', 'kinesthetic']);
            $table->enum('detection_method', ['quiz', 'explicit', 'behavioral', 'default']);
            $table->decimal('confidence_score', 3, 2)->nullable();
            $table->json('quiz_results')->nullable();
            $table->timestamps();
            
            $table->index(['session_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_style_preferences');
    }
};
