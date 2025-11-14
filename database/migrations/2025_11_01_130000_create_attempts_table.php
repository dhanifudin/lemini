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
        // Redundant duplicate of attempts table creation kept for historical reference.
        // Intentionally left no-op to avoid re-creating an existing table.
        // Use later ALTER migrations for schema changes instead of duplicate CREATE.
        return; // no-op
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: original create migration handles dropping. This duplicate should not reverse anything.
        return; // no-op
    }
};