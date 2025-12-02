<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Models\Task;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')->required(),
            Textarea::make('description')->rows(4),
            Select::make('status')
                ->options(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'])
                ->default('pending'),
            Select::make('priority')
                ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                ->default('medium'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('status'),
                TextColumn::make('priority'),
                TextColumn::make('due_date')->date(),
            ])
            ->filters([])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\TaskResource\Pages\ListTasks::route('/'),
            'create' => \App\Filament\Resources\TaskResource\Pages\CreateTask::route('/create'),
            'edit' => \App\Filament\Resources\TaskResource\Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
