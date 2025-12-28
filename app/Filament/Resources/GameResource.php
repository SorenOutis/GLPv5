<?php

namespace App\Filament\Resources;

use App\Models\Game;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationLabel = 'Games';
    protected static string | \UnitEnum | null $navigationGroup = 'Gamification';
    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Game Name')
                ->required()
                ->maxLength(255)
                ->columnSpan('full'),

            Textarea::make('description')
                ->label('Description')
                ->required()
                ->maxLength(500)
                ->rows(3)
                ->columnSpan('full'),

            TextInput::make('category')
                ->label('Category')
                ->required()
                ->maxLength(100)
                ->placeholder('e.g., Coding, Web Development'),

            Select::make('difficulty')
                ->label('Difficulty')
                ->options([
                    'Beginner' => 'Beginner',
                    'Intermediate' => 'Intermediate',
                    'Advanced' => 'Advanced',
                ])
                ->required(),

            TextInput::make('thumbnail')
                ->label('Thumbnail URL')
                ->required()
                ->url()
                ->placeholder('https://example.com/image.jpg')
                ->columnSpan('full'),

            TextInput::make('badge')
                ->label('Badge Emoji')
                ->required()
                ->maxLength(10)
                ->placeholder('e.g., 🎮')
                ->helperText('Single emoji or short text'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true)
                ->helperText('Inactive games will not appear on the user Games page'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Game Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('category')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('difficulty')
                    ->label('Difficulty')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Beginner' => 'success',
                        'Intermediate' => 'warning',
                        'Advanced' => 'danger',
                    }),

                TextColumn::make('badge')
                    ->label('Badge')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('difficulty')
                    ->options([
                        'Beginner' => 'Beginner',
                        'Intermediate' => 'Intermediate',
                        'Advanced' => 'Advanced',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->queries(
                        true: fn ($query) => $query->where('is_active', true),
                        false: fn ($query) => $query->where('is_active', false),
                    ),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\GameResource\Pages\ListGames::route('/'),
            'create' => \App\Filament\Resources\GameResource\Pages\CreateGame::route('/create'),
            'edit' => \App\Filament\Resources\GameResource\Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
