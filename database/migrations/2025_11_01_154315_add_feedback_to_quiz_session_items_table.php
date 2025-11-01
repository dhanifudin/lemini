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
        Schema::table('quiz_session_items', function (Blueprint $table) {
            $table->json('feedback')->nullable()->after('status');
            $table->boolean('flagged')->default(false)->after('feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_session_items', function (Blueprint $table) {
            $table->dropColumn(['feedback', 'flagged']);
        });
    }
};
