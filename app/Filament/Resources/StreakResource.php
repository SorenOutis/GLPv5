<?php

namespace App\Filament\Resources;

use App\Models\Streak;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class StreakResource extends Resource
{
    protected static ?string $model = Streak::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationLabel = 'Streaks';

    protected static ?string $pluralModelLabel = 'Streaks';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Streak Information')
                ->description('View and manage user streaks')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('User'),

                    TextInput::make('current_streak')
                        ->numeric()
                        ->default(0)
                        ->label('Current Streak (days)')
                        ->hint('Consecutive days logged in'),

                    TextInput::make('longest_streak')
                        ->numeric()
                        ->default(0)
                        ->label('Longest Streak (days)')
                        ->hint('Longest streak achieved'),

                    DatePicker::make('last_login_date')
                        ->label('Last Login Date'),

                    DateTimePicker::make('last_login_at')
                        ->label('Last Login Time')
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('User Name'),

                TextColumn::make('user.email')
                    ->searchable()
                    ->label('Email'),

                BadgeColumn::make('current_streak')
                    ->label('Current Streak')
                    ->color(fn (int $state): string => match (true) {
                        $state >= 30 => 'success',
                        $state >= 14 => 'info',
                        $state >= 7 => 'warning',
                        default => 'gray',
                    })
                    ->icon('heroicon-m-fire')
                    ->formatStateUsing(fn ($state) => "{$state} days"),

                BadgeColumn::make('longest_streak')
                    ->label('Longest Streak')
                    ->color('warning')
                    ->icon('heroicon-m-star')
                    ->formatStateUsing(fn ($state) => "{$state} days"),

                TextColumn::make('last_login_date')
                    ->date()
                    ->label('Last Login Date')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('active_streak')
                    ->label('Has Active Streak (>0 days)')
                    ->query(fn (Builder $query): Builder => $query->where('current_streak', '>', 0)),

                \Filament\Tables\Filters\Filter::make('high_streak')
                    ->label('High Streak (>= 30 days)')
                    ->query(fn (Builder $query): Builder => $query->where('current_streak', '>=', 30)),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('current_streak', 'desc');
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
