<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Models\Announcement;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Announcements';
    protected static ?string $label = 'Announcement';
    protected static ?string $pluralLabel = 'Announcements';
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')
                ->required()
                ->label('Announcement Title')
                ->maxLength(255)
                ->columnSpanFull(),
            
            Textarea::make('description')
                ->label('Short Description')
                ->rows(2)
                ->maxLength(500)
                ->helperText('Brief summary of the announcement')
                ->columnSpanFull(),
            
            RichEditor::make('content')
                ->required()
                ->label('Content')
                ->columnSpanFull(),
            
            DateTimePicker::make('published_at')
                ->label('Publish Date & Time')
                ->helperText('When this announcement should be published'),
            
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
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('font-semibold'),
                
                TextColumn::make('description')
                    ->limit(50)
                    ->label('Summary'),
                
                TextColumn::make('user.name')
                    ->label('Posted By')
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Published'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created'),
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
            'index' => \App\Filament\Resources\AnnouncementResource\Pages\ListAnnouncements::route('/'),
            'create' => \App\Filament\Resources\AnnouncementResource\Pages\CreateAnnouncement::route('/create'),
            'edit' => \App\Filament\Resources\AnnouncementResource\Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
