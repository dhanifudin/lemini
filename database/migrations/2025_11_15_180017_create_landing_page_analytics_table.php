<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_page_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('learning_style')->nullable();
            $table->integer('visits')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('avg_time_on_page', 8, 2)->nullable();
            $table->decimal('bounce_rate', 5, 2)->nullable();
            $table->integer('interaction_count')->default(0);
            $table->timestamps();
            $table->index(['date', 'learning_style']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_analytics');
    }
};
