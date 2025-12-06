<?php

namespace App\Filament\Resources\SystemLogoResource\Pages;

use App\Filament\Resources\SystemLogoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSystemLogo extends CreateRecord
{
    protected static string $resource = SystemLogoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['is_active'] ?? false) {
            // Deactivate all other logos
            $this->getModel()::query()->update(['is_active' => false]);
        }

        return $data;
    }
}
