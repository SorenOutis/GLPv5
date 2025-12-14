<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Models\Streak;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class StreakResource extends Resource
{
    protected static ?string $model = Streak::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-fire';
    protected static ?string $navigationLabel = 'Streak';
    protected static string|UnitEnum|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->required()
                ->searchable()
                ->preload(),
            TextInput::make('current_streak')
                ->label('Current Streak (Days)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->helperText('The current consecutive days of activity'),
            TextInput::make('longest_streak')
                ->label('Longest Streak (Days)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->helperText('The longest streak the user has achieved'),
            DateTimePicker::make('last_login_date')
                ->label('Last Login Date')
                ->helperText('The date of the last login'),
            DateTimePicker::make('last_login_at')
                ->label('Last Login At')
                ->helperText('The timestamp of the last login'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('current_streak')
                    ->label('Current Streak')
                    ->numeric()
                    ->sortable()
                    ->suffix(' days'),
                TextColumn::make('longest_streak')
                    ->label('Longest Streak')
                    ->numeric()
                    ->sortable()
                    ->suffix(' days'),
                TextColumn::make('last_login_date')
                    ->label('Last Login Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('last_login_at')
                    ->label('Last Login At')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\StreakResource\Pages\ListStreaks::route('/'),
            'create' => \App\Filament\Resources\StreakResource\Pages\CreateStreak::route('/create'),
            'edit' => \App\Filament\Resources\StreakResource\Pages\EditStreak::route('/{record}/edit'),
        ];
    }
}
