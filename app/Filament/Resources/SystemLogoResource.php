<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemLogoResource\Pages;
use App\Models\SystemLogo;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Table;

class SystemLogoResource extends Resource
{
    protected static ?string $model = SystemLogo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'System Logo';

    protected static ?string $modelLabel = 'System Logo';

    protected static ?string $pluralModelLabel = 'System Logos';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Logo Name')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g., LevelUp Academy Logo v1')
                ->helperText('Give this logo configuration a descriptive name'),

            FileUpload::make('logo_path')
                ->label('Light Mode Logo')
                ->image()
                ->disk('public')
                ->directory('logos')
                ->visibility('public')
                ->required()
                ->maxSize(5 * 1024) // 5MB
                ->helperText('Upload your logo for light mode (PNG, JPG, SVG - Max 5MB)')
                ->columnSpanFull(),

            FileUpload::make('logo_dark_path')
                ->label('Dark Mode Logo')
                ->image()
                ->disk('public')
                ->directory('logos')
                ->visibility('public')
                ->maxSize(5 * 1024) // 5MB
                ->helperText('Upload your logo for dark mode (PNG, JPG, SVG - Max 5MB)')
                ->columnSpanFull(),

            FileUpload::make('favicon_path')
                ->label('Favicon (Optional)')
                ->image()
                ->disk('public')
                ->directory('logos')
                ->visibility('public')
                ->maxSize(2 * 1024) // 2MB
                ->helperText('Upload your favicon (ICO, PNG - Max 2MB)')
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label('Activate This Logo')
                ->helperText('Only one logo can be active at a time. Activating this will deactivate others.')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Light Logo')
                    ->size(50)
                    ->searchable(),

                ImageColumn::make('logo_dark_path')
                    ->label('Dark Logo')
                    ->size(50)
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Logo Name')
                    ->searchable()
                    ->sortable(),

                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('is_active', 'desc')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemLogos::route('/'),
            'create' => Pages\CreateSystemLogo::route('/create'),
            'edit' => Pages\EditSystemLogo::route('/{record}/edit'),
        ];
    }
}
