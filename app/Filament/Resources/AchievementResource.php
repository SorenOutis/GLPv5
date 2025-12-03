<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Models\Achievement;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Achievements';
    protected static ?string $label = 'Achievement';
    protected static ?string $pluralLabel = 'Achievements';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->required()
                ->label('Achievement Name')
                ->maxLength(255),
            
            Textarea::make('description')
                ->required()
                ->rows(3)
                ->label('Description'),
            
            TextInput::make('icon')
                ->required()
                ->label('Icon (Emoji)')
                ->maxLength(10)
                ->hint('Enter an emoji character'),
            
            Select::make('category')
                ->required()
                ->options([
                    'Learning' => 'Learning',
                    'Streak' => 'Streak',
                    'Milestones' => 'Milestones',
                    'Levels' => 'Levels',
                    'XP' => 'XP',
                    'Social' => 'Social',
                    'Special' => 'Special',
                ])
                ->label('Category')
                ->preload(),
            
            Select::make('difficulty')
                ->required()
                ->options([
                    'Easy' => 'Easy',
                    'Medium' => 'Medium',
                    'Hard' => 'Hard',
                    'Legendary' => 'Legendary',
                ])
                ->label('Difficulty'),
            
            TextInput::make('xp_reward')
                ->numeric()
                ->required()
                ->minValue(0)
                ->label('XP Reward'),
            
            Toggle::make('is_active')
                ->default(true)
                ->label('Active')
                ->helperText('Deactivate to hide from users'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label('Icon')
                    ->width('80px'),
                
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('category')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Learning' => 'blue',
                        'Streak' => 'red',
                        'Milestones' => 'purple',
                        'Levels' => 'green',
                        'XP' => 'yellow',
                        'Social' => 'pink',
                        'Special' => 'cyan',
                        default => 'gray',
                    }),
                
                TextColumn::make('difficulty')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Easy' => 'green',
                        'Medium' => 'blue',
                        'Hard' => 'orange',
                        'Legendary' => 'purple',
                        default => 'gray',
                    }),
                
                TextColumn::make('xp_reward')
                    ->label('XP')
                    ->sortable()
                    ->suffix(' XP'),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Learning' => 'Learning',
                        'Streak' => 'Streak',
                        'Milestones' => 'Milestones',
                        'Levels' => 'Levels',
                        'XP' => 'XP',
                        'Social' => 'Social',
                        'Special' => 'Special',
                    ])
                    ->preload(),
                
                SelectFilter::make('difficulty')
                    ->options([
                        'Easy' => 'Easy',
                        'Medium' => 'Medium',
                        'Hard' => 'Hard',
                        'Legendary' => 'Legendary',
                    ])
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                // Bulk actions can be added here
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AchievementResource\Pages\ListAchievements::route('/'),
            'create' => \App\Filament\Resources\AchievementResource\Pages\CreateAchievement::route('/create'),
            'edit' => \App\Filament\Resources\AchievementResource\Pages\EditAchievement::route('/{record}/edit'),
        ];
    }
}
