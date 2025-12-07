<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Models\CommunityPost;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommunityPostResource extends Resource
{
    protected static ?string $model = CommunityPost::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Community Posts';
    protected static ?string $label = 'Post';
    protected static ?string $pluralLabel = 'Community Posts';
    protected static string|UnitEnum|null $navigationGroup = 'Community';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')
                ->required()
                ->label('Post Title')
                ->maxLength(255)
                ->columnSpanFull(),
            
            Textarea::make('content')
                ->required()
                ->label('Content')
                ->rows(6)
                ->columnSpanFull(),
            
            Textarea::make('excerpt')
                ->label('Excerpt')
                ->rows(2)
                ->maxLength(255)
                ->helperText('Leave blank to auto-generate')
                ->columnSpanFull(),

            Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->default('pending')
                ->required(),

            Toggle::make('is_approved')
                ->label('Approved')
                ->default(false),

            Toggle::make('is_pinned')
                ->label('Pin to Top')
                ->default(false),

            TextInput::make('views')
                ->numeric()
                ->default(0),

            TextInput::make('likes')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_pinned')
                    ->boolean()
                    ->label('Pinned')
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('font-semibold')
                    ->limit(50),
                
                TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_approved')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                
                TextColumn::make('views_count')
                    ->label('👁️ Views')
                    ->getStateUsing(function ($record) {
                        return $record->views()->count();
                    }),

                TextColumn::make('reactions_like_count')
                    ->label('👍 Likes')
                    ->getStateUsing(function ($record) {
                        return $record->reactions()->where('reaction_type', 'like')->count();
                    }),

                TextColumn::make('reactions_love_count')
                    ->label('❤️ Loves')
                    ->getStateUsing(function ($record) {
                        return $record->reactions()->where('reaction_type', 'love')->count();
                    }),

                TextColumn::make('reactions_haha_count')
                    ->label('😂 Haha')
                    ->getStateUsing(function ($record) {
                        return $record->reactions()->where('reaction_type', 'haha')->count();
                    }),

                TextColumn::make('reactions_wow_count')
                    ->label('😮 Wow')
                    ->getStateUsing(function ($record) {
                        return $record->reactions()->where('reaction_type', 'wow')->count();
                    }),

                TextColumn::make('reactions_sad_count')
                    ->label('😢 Sad')
                    ->getStateUsing(function ($record) {
                        return $record->reactions()->where('reaction_type', 'sad')->count();
                    }),

                TextColumn::make('reactions_angry_count')
                    ->label('😠 Angry')
                    ->getStateUsing(function ($record) {
                        return $record->reactions()->where('reaction_type', 'angry')->count();
                    }),

                TextColumn::make('reactions_total_count')
                    ->label('Total Reactions')
                    ->getStateUsing(function ($record) {
                        return $record->reactions()->count();
                    }),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                // Bulk actions can be added here
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CommunityPostResource\Pages\ListCommunityPosts::route('/'),
            'create' => \App\Filament\Resources\CommunityPostResource\Pages\CreateCommunityPost::route('/create'),
            'edit' => \App\Filament\Resources\CommunityPostResource\Pages\EditCommunityPost::route('/{record}/edit'),
        ];
    }
}
