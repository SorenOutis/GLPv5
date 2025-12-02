<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')->required(),
            TextInput::make('email')->email()->required(),
            TextInput::make('password')->password()->required(fn ($context) => $context === 'create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
