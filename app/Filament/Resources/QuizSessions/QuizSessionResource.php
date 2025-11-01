<?php

namespace App\Filament\Resources\QuizSessions;

use App\Filament\Resources\QuizSessions\Pages\ListQuizSessions;
use App\Filament\Resources\QuizSessions\Pages\ViewQuizSession;
use App\Filament\Resources\QuizSessions\Tables\QuizSessionsTable;
use App\Models\QuizSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuizSessionResource extends Resource
{
    protected static ?string $model = QuizSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Quiz Sessions';

    protected static ?string $modelLabel = 'Quiz Session';

    protected static ?string $pluralModelLabel = 'Quiz Sessions';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return QuizSessionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuizSessions::route('/'),
            'view' => ViewQuizSession::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
