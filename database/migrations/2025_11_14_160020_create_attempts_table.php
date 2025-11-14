<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Third duplicate of attempts table create. Neutralized to prevent conflicts.
        return; // no-op
    }

    public function down(): void
    {
        // No-op: avoid dropping via duplicate migration.
        return; // no-op
    }
};