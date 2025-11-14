<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_id')->nullable()->constrained()->onDelete('set null');
            $table->string('objective_code');
            $table->text('stem');
            $table->string('type');
            $table->json('options')->nullable();
            $table->text('answer');
            $table->text('rationale')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};