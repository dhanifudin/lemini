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
        Schema::create('quiz_session_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->json('response')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['quiz_session_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_session_items');
    }
};
