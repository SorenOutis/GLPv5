<?php

namespace App\Filament\Resources;

use App\Models\Assignment;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')
                ->label('Assignment Title')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Description')
                ->maxLength(1000)
                ->rows(4),
            Select::make('category')
                ->label('Category')
                ->options([
                    'python' => 'Python',
                    'web-development' => 'Web Development',
                    'data-science' => 'Data Science',
                    'mobile' => 'Mobile Development',
                    'devops' => 'DevOps',
                    'other' => 'Other',
                ])
                ->searchable(),
            FileUpload::make('file_path')
                ->label('Upload File')
                ->disk('public')
                ->directory('assignments')
                ->maxSize(102400) // 100MB
                ->visibility('public')
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/plain',
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                ]),
            DateTimePicker::make('due_date')
                ->label('Due Date'),
            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50),
                TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AssignmentResource\Pages\ListAssignments::route('/'),
            'create' => \App\Filament\Resources\AssignmentResource\Pages\CreateAssignment::route('/create'),
            'edit' => \App\Filament\Resources\AssignmentResource\Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
