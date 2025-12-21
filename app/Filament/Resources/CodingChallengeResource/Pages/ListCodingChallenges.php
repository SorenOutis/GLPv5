<?php

namespace App\Filament\Resources\CodingChallengeResource\Pages;

use App\Filament\Resources\CodingChallengeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCodingChallenges extends ListRecords
{
    protected static string $resource = CodingChallengeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
