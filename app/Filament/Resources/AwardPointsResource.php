<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\NotificationService;

class AwardPointsResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Award Points';
    protected static string|UnitEnum|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                        ->label('User Name')
                        ->disabled()
                        ->columnSpan(1),
                    TextInput::make('email')
                        ->label('Email')
                        ->disabled()
                        ->columnSpan(1),
                ]),
            Grid::make(2)
                ->schema([
                    TextInput::make('profile.total_xp')
                        ->label('Current Total XP')
                        ->numeric()
                        ->disabled()
                        ->columnSpan(1),
                    TextInput::make('profile.level')
                        ->label('Current Level')
                        ->numeric()
                        ->disabled()
                        ->columnSpan(1),
                ]),
            TextInput::make('points_to_award')
                ->label('Points/XP to Award')
                ->numeric()
                ->required()
                ->minValue(1)
                ->helperText('Enter the amount of XP points to award to this user'),
            Textarea::make('points_reason')
                ->label('Reason for Award (Optional)')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Add a note explaining why these points are being awarded'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('profile.level')
                    ->label('Level')
                    ->numeric()
                    ->sortable()
                    ->default('—'),
                TextColumn::make('profile.total_xp')
                    ->label('Total XP')
                    ->numeric()
                    ->sortable()
                    ->default('—'),
                TextColumn::make('profile.streak_days')
                    ->label('Streak')
                    ->numeric()
                    ->sortable()
                    ->suffix(' days')
                    ->default('—'),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Action::make('award_points')
                    ->label('Award Points')
                    ->icon('heroicon-o-star')
                    ->form([
                        TextInput::make('points_amount')
                            ->label('Points to Award')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(100),
                        Textarea::make('award_reason')
                            ->label('Reason (Optional)')
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->action(function (User $record, array $data): void {
                         $pointsAmount = (int) $data['points_amount'];
                         
                         // Refresh the record to ensure we have latest data
                         $record->refresh();
                         
                         // Get or create profile with user_id explicitly set
                         $profile = $record->profile()->firstOrCreate([], [
                             'total_xp' => 0,
                             'level' => 1,
                             'current_level_xp' => 0,
                             'xp_for_next_level' => 1000,
                             'streak_days' => 0,
                             'rank_title' => 'Plastic',
                         ]);

                         $oldLevel = $profile->level;
                         $newTotalXp = $profile->total_xp + $pointsAmount;
                         $newLevel = max(1, intval($newTotalXp / 100) + 1);
                         $newCurrentLevelXp = $newTotalXp % 100;

                         // Update profile object in memory first to calculate correct rank title
                         $profile->level = $newLevel;
                         $newRankTitle = $profile->calculateRankTitle();

                         $profile->update([
                             'total_xp' => $newTotalXp,
                             'level' => $newLevel,
                             'current_level_xp' => $newCurrentLevelXp,
                             'rank_title' => $newRankTitle,
                         ]);

                         // Send user notification for XP reward
                         NotificationService::notifyXPGained($record, $pointsAmount, 'Admin Award');

                         // Notify level up if level increased
                         if ($newLevel > $oldLevel) {
                             NotificationService::notifyLevelUp($record, $newLevel, $pointsAmount);
                         }

                         Notification::make()
                             ->title('Points Awarded Successfully')
                             ->body("Awarded {$pointsAmount} XP to {$record->name} (Level {$newLevel})")
                             ->success()
                             ->send();
                     }),
                Action::make('edit_xp')
                    ->label('Edit XP')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        TextInput::make('profile.total_xp')
                            ->label('Total XP')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                        Textarea::make('edit_reason')
                            ->label('Reason for Edit (Optional)')
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->action(function (User $record, array $data): void {
                         $newTotalXp = (int) $data['profile']['total_xp'];
                         
                         // Refresh the record to ensure we have latest data
                         $record->refresh();
                         
                         // Get or create profile
                         $profile = $record->profile()->firstOrCreate([], [
                             'total_xp' => 0,
                             'level' => 1,
                             'current_level_xp' => 0,
                             'xp_for_next_level' => 1000,
                             'streak_days' => 0,
                             'rank_title' => 'Plastic',
                         ]);

                         $oldLevel = $profile->level;
                         // Calculate level and current level XP
                         $newLevel = max(1, intval($newTotalXp / 100) + 1);
                         $newCurrentLevelXp = $newTotalXp % 100;

                         // Update profile object in memory first to calculate correct rank title
                         $profile->level = $newLevel;
                         $newRankTitle = $profile->calculateRankTitle();

                         $profile->update([
                             'total_xp' => $newTotalXp,
                             'level' => $newLevel,
                             'current_level_xp' => $newCurrentLevelXp,
                             'rank_title' => $newRankTitle,
                         ]);

                         // Calculate the difference and send notification if XP increased
                         $xpDifference = $newTotalXp - $profile->getOriginal('total_xp');
                         if ($xpDifference > 0) {
                             NotificationService::notifyXPGained($record, $xpDifference, 'Admin Adjustment');

                             // Notify level up if level increased
                             if ($newLevel > $oldLevel) {
                                 NotificationService::notifyLevelUp($record, $newLevel, $xpDifference);
                             }
                         }

                         Notification::make()
                             ->title('XP Updated Successfully')
                             ->body("{$record->name} XP set to {$newTotalXp} (Level {$newLevel})")
                             ->success()
                             ->send();
                     }),
                        ])
                        ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AwardPointsResource\Pages\ListAwardPoints::route('/'),
        ];
    }
}
