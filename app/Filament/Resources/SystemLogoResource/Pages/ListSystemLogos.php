<?php

namespace App\Filament\Resources\SystemLogoResource\Pages;

use App\Filament\Resources\SystemLogoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSystemLogos extends ListRecords
{
    protected static string $resource = SystemLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Upload New Logo'),
        ];
    }
}
