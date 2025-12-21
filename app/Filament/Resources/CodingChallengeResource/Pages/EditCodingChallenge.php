<?php

namespace App\Filament\Resources\CodingChallengeResource\Pages;

use App\Filament\Resources\CodingChallengeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCodingChallenge extends EditRecord
{
    protected static string $resource = CodingChallengeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
