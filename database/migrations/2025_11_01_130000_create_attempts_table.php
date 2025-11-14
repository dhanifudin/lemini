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
        // Ensure the referenced tables exist before creating attempts.
        if (!Schema::hasTable('items')) {
            throw new RuntimeException('Cannot create attempts table: items table does not exist yet.');
        }
        if (!Schema::hasTable('users')) {
            throw new RuntimeException('Cannot create attempts table: users table does not exist yet.');
        }

        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            // Explicit foreign key declarations for clarity
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            $table->text('response');
            $table->float('score')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Optional composite index to speed up queries by user & item
            $table->index(['user_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};