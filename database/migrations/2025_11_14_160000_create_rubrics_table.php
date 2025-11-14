<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Duplicate CREATE of rubrics table. Original earlier migration already defines schema.
        // Converted to no-op to preserve migration history without causing conflicts.
        return; // no-op
    }

    public function down(): void
    {
        // No-op: do not drop table from duplicate migration.
        return; // no-op
    }
};