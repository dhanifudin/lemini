<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_session_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_session_id')->constrained('quiz_sessions')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->json('response')->nullable();
            $table->float('score')->nullable();
            $table->string('status')->default('pending');
            $table->json('feedback')->nullable();
            $table->boolean('flagged')->default(false);
            $table->timestamps();
            $table->index(['quiz_session_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_session_items');
    }
};
