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
        Schema::table('quiz_sessions', function (Blueprint $table) {
            $table->decimal('average_score', 5, 2)->nullable()->after('submitted_at');
            $table->unsignedInteger('correct_count')->default(0)->after('average_score');
            $table->unsignedInteger('incorrect_count')->default(0)->after('correct_count');
            $table->unsignedInteger('pending_review_count')->default(0)->after('incorrect_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_sessions', function (Blueprint $table) {
            $table->dropColumn(['average_score', 'correct_count', 'incorrect_count', 'pending_review_count']);
        });
    }
};
