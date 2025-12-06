<?php

namespace App\Filament\Resources\SystemLogoResource\Pages;

use App\Filament\Resources\SystemLogoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSystemLogo extends EditRecord
{
    protected static string $resource = SystemLogoResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['is_active'] ?? false) {
            // Deactivate all other logos except this one
            $this->record::query()
                ->where('id', '!=', $this->record->id)
                ->update(['is_active' => false]);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
