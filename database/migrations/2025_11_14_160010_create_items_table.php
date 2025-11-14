<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Duplicate CREATE of items table. Earlier migration already applied.
        return; // no-op
    }

    public function down(): void
    {
        // No-op so as not to drop table via duplicate.
        return; // no-op
    }
};