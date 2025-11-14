<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_behaviors', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('event_type');
            $table->json('event_data')->nullable();
            $table->timestamp('event_timestamp')->nullable();
            // Note: Model disables timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_behaviors');
    }
};
