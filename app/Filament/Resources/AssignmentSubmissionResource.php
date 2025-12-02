<?php

namespace App\Filament\Resources;

use App\Models\AssignmentSubmission;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssignmentSubmissionResource extends Resource
{
    protected static ?string $model = AssignmentSubmission::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Submissions';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('assignment_id')
                ->relationship('assignment', 'title')
                ->required()
                ->searchable(),
            Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->searchable(),
            Textarea::make('notes')
                ->label('Student Notes')
                ->rows(4),
            Select::make('status')
                ->options([
                    'submitted' => 'Submitted',
                    'pending' => 'Pending Review',
                    'graded' => 'Graded',
                ])
                ->default('submitted'),
            TextInput::make('grade')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->suffix('%'),
            Textarea::make('feedback')
                ->label('Instructor Feedback')
                ->rows(4),
            FileUpload::make('file_path')
                ->label('Submission File')
                ->disk('public')
                ->directory('submissions')
                ->visibility('public'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignment.title')
                    ->label('Assignment')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'submitted',
                        'info' => 'pending',
                        'success' => 'graded',
                    ])
                    ->sortable(),
                TextColumn::make('grade')
                    ->label('Grade')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'pending' => 'Pending Review',
                        'graded' => 'Graded',
                    ]),
                SelectFilter::make('assignment_id')
                    ->relationship('assignment', 'title'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AssignmentSubmissionResource\Pages\ListAssignmentSubmissions::route('/'),
            'create' => \App\Filament\Resources\AssignmentSubmissionResource\Pages\CreateAssignmentSubmission::route('/create'),
            'edit' => \App\Filament\Resources\AssignmentSubmissionResource\Pages\EditAssignmentSubmission::route('/{record}/edit'),
        ];
    }
}
