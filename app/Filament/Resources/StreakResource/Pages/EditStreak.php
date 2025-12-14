<?php

namespace App\Filament\Resources\StreakResource\Pages;

use App\Filament\Resources\StreakResource;
use Filament\Resources\Pages\EditRecord;

class EditStreak extends EditRecord
{
    protected static string $resource = StreakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
