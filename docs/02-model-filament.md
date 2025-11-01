# Filament Resource Integration Plan

This document outlines the plan to integrate our models with the Filament admin panel by creating a Resource for each one, including user management.

## 1. Generate Filament Resources

First, we will generate a Filament Resource for each model. Run these commands in your terminal:

```bash
php artisan make:filament-resource User
php artisan make:filament-resource Rubric
php artisan make:filament-resource Item
php artisan make:filament-resource Attempt
php artisan make:filament-resource Feedback
php artisan make:filament-resource Mastery
php artisan make:filament-resource Recommendation
```

These commands will create new files in the `app/Filament/Resources` directory.

## 2. Define Resource Schemas

Next, we will update the `form()` and `table()` methods in each generated resource file. Below are the code snippets to use for each resource.

--- 

### UserResource

File: `app/Filament/Resources/UserResource.php`

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\Select::make('role')
                ->options([
                    'student' => 'Student',
                    'teacher' => 'Teacher',
                    'admin' => 'Admin',
                ])
                ->default('student')
                ->required(),
            Forms\Components\DateTimePicker::make('email_verified_at'),
            Forms\Components\TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create'),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('email_verified_at')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            //
        ]);
}
```

--- 

### RubricResource

File: `app/Filament/Resources/RubricResource.php`

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Rubric;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Textarea::make('criteria')->required()->rows(5),
            Forms\Components\Textarea::make('levels')->required()->rows(5),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}
```

--- 

### ItemResource

File: `app/Filament/Resources/ItemResource.php`

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Item;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('rubric_id')
                ->relationship('rubric', 'name'),
            Forms\Components\TextInput::make('objective_code')->required(),
            Forms\Components\Textarea::make('stem')->required()->columnSpanFull(),
            Forms\Components\Select::make('type')->options([
                'MCQ' => 'Multiple Choice',
                'SAQ' => 'Short Answer',
            ])->required(),
            Forms\Components\Textarea::make('options')->rows(5),
            Forms\Components\Textarea::make('answer')->required()->rows(3),
            Forms\Components\Textarea::make('rationale')->rows(3),
            Forms\Components\Textarea::make('meta')->rows(3),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('stem')->limit(50)->searchable(),
            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('objective_code')->searchable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            //
        ]);
}
```

--- 

### AttemptResource

File: `app/Filament/Resources/AttemptResource.php`

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Attempt;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'name')->required(),
            Forms\Components\Select::make('item_id')->relationship('item', 'stem')->required(),
            Forms\Components\Textarea::make('response')->required()->columnSpanFull(),
            Forms\Components\TextInput::make('score')->numeric(),
            Forms\Components\Textarea::make('metadata')->columnSpanFull(),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('user.name')->searchable(),
            Tables\Columns\TextColumn::make('item.stem')->limit(50)->searchable(),
            Tables\Columns\TextColumn::make('score')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            //
        ]);
}
```

--- 

### FeedbackResource

File: `app/Filament/Resources/FeedbackResource.php`

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Feedback;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('attempt_id')->relationship('attempt', 'id')->required(),
            Forms\Components\Textarea::make('ai_text')->columnSpanFull(),
            Forms\Components\Textarea::make('human_revision')->columnSpanFull(),
            Forms\Components\Select::make('status')->options([
                'draft' => 'Draft',
                'published' => 'Published',
            ])->required(),
            Forms\Components\DateTimePicker::make('released_at'),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('attempt.id'),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('released_at')->dateTime(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            //
        ]);
}
```

--- 

### MasteryResource

File: `app/Filament/Resources/MasteryResource.php`

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Mastery;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'name')->required(),
            Forms\Components\TextInput::make('objective_code')->required(),
            Forms\Components\TextInput::make('level')->required(),
            Forms\Components\DateTimePicker::make('last_seen_at')->required(),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('user.name')->searchable(),
            Tables\Columns\TextColumn::make('objective_code')->searchable(),
            Tables\Columns\TextColumn::make('level'),
            Tables\Columns\TextColumn::make('last_seen_at')->dateTime(),
        ])
        ->filters([
            //
        ]);
}
```

--- 

### RecommendationResource

File: `app/Filament/Resources/RecommendationResource.php`

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Recommendation;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'name')->required(),
            Forms\Components\Textarea::make('payload')->required()->columnSpanFull(),
            Forms\Components\Checkbox::make('chosen'),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('user.name')->searchable(),
            Tables\Columns\IconColumn::make('chosen')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            //
        ]);
}
```
