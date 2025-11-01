# Models and Migrations Plan

This document outlines the plan for creating the necessary database models and migrations based on the requirements in `GEMINI.md`.

## Artisan Commands to Generate Files

First, we will generate all the necessary model and migration files using the following artisan commands:

```bash
php artisan make:model Rubric -m
php artisan make:model Item -m
php artisan make:model Attempt -m
php artisan make:model Feedback -m
php artisan make:model Mastery -m
php artisan make:model Recommendation -m
```

## Migration Definitions

The following sections detail the schema for each migration file.

### 1. `rubrics`

File: `database/migrations/YYYY_MM_DD_HHMMSS_create_rubrics_table.php`

```php
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
        Schema::create('rubrics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('criteria');
            $table->json('levels');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubrics');
    }
};
```

### 2. `items`

File: `database/migrations/YYYY_MM_DD_HHMMSS_create_items_table.php`

```php
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
```

### 3. `attempts`

File: `database/migrations/YYYY_MM_DD_HHMMSS_create_attempts_table.php`

```php
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
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->text('response');
            $table->float('score')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
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
```

### 4. `feedback`

File: `database/migrations/YYYY_MM_DD_HHMMSS_create_feedback_table.php`

```php
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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained()->onDelete('cascade');
            $table->json('ai_text')->nullable();
            $table->text('human_revision')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
```

### 5. `masteries`

File: `database/migrations/YYYY_MM_DD_HHMMSS_create_masteries_table.php`

```php
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
        Schema::create('masteries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('objective_code');
            $table->string('level');
            $table->timestamp('last_seen_at');
            $table->timestamps();

            $table->unique(['user_id', 'objective_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masteries');
    }
};
```

### 6. `recommendations`

File: `database/migrations/YYYY_MM_DD_HHMMSS_create_recommendations_table.php`

```php
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
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('payload');
            $table->boolean('chosen')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
```
