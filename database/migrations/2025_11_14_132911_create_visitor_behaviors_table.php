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
        Schema::create('visitor_behaviors', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('event_type', 50)->index();
            $table->json('event_data');
            $table->timestamp('event_timestamp')->index();
            
            $table->index(['session_id', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_behaviors');
    }
};
