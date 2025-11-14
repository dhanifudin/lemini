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
        Schema::create('landing_page_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('learning_style', ['visual', 'auditory', 'kinesthetic', 'unknown']);
            $table->integer('visits')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('avg_time_on_page', 10, 2)->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            $table->integer('interaction_count')->default(0);
            $table->timestamps();
            
            $table->unique(['date', 'learning_style']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_analytics');
    }
};
