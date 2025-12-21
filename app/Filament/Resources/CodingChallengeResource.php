<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Models\CodingChallenge;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CodingChallengeResource extends Resource
{
    protected static ?string $model = CodingChallenge::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationLabel = 'Coding Challenges';
    protected static string|UnitEnum|null $navigationGroup = 'Gamification';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')
                ->required()
                ->label('Challenge Title')
                ->placeholder('e.g., Hello World, Fibonacci Sequence'),

            Textarea::make('description')
                ->required()
                ->rows(3)
                ->label('Short Description')
                ->placeholder('Brief overview of the challenge'),

            Textarea::make('problem_statement')
                ->required()
                ->rows(4)
                ->label('Problem Statement')
                ->placeholder('Detailed explanation of what the user needs to solve'),

            Repeater::make('requirements')
                ->simple(
                    Textarea::make('requirement')
                        ->placeholder('e.g., Write clean, readable code')
                        ->rows(2)
                )
                ->label('Requirements')
                ->helperText('Add each requirement as a separate item'),

            Textarea::make('example_input')
                ->label('Example Input')
                ->placeholder('Input: sample_input')
                ->rows(2),

            Textarea::make('example_output')
                ->label('Example Output')
                ->placeholder('Output: expected_output')
                ->rows(2),

            Repeater::make('tips')
                ->simple(
                    Textarea::make('tip')
                        ->placeholder('e.g., Start with the simplest approach')
                        ->rows(2)
                )
                ->label('Tips')
                ->helperText('Add helpful tips for solving the challenge'),

            Select::make('difficulty')
                ->required()
                ->options([
                    'Beginner' => 'Beginner',
                    'Intermediate' => 'Intermediate',
                    'Advanced' => 'Advanced',
                ])
                ->label('Difficulty Level'),

            Select::make('language')
                ->required()
                ->options([
                    'JavaScript' => 'JavaScript',
                    'Python' => 'Python',
                    'Java' => 'Java',
                    'C++' => 'C++',
                    'TypeScript' => 'TypeScript',
                    'Go' => 'Go',
                ])
                ->label('Programming Language'),

            TextInput::make('category')
                ->required()
                ->label('Category')
                ->placeholder('e.g., Basics, Functions, Algorithms, Strings'),

            TextInput::make('xp_reward')
                ->required()
                ->numeric()
                ->minValue(0)
                ->label('XP Reward')
                ->placeholder('e.g., 50, 150, 250'),

            Toggle::make('is_active')
                ->default(true)
                ->label('Active'),

            DatePicker::make('start_date')
                ->label('Start Date'),

            DatePicker::make('end_date')
                ->label('End Date'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('difficulty')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Beginner' => 'success',
                        'Intermediate' => 'warning',
                        'Advanced' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('language')
                    ->sortable(),
                TextColumn::make('category')
                    ->sortable(),
                TextColumn::make('xp_reward')
                    ->label('XP Reward')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CodingChallengeResource\Pages\ListCodingChallenges::route('/'),
            'create' => \App\Filament\Resources\CodingChallengeResource\Pages\CreateCodingChallenge::route('/create'),
            'edit' => \App\Filament\Resources\CodingChallengeResource\Pages\EditCodingChallenge::route('/{record}/edit'),
        ];
    }
}
