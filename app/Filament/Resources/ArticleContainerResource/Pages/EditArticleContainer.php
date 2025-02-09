<?php

namespace App\Filament\Resources\ArticleContainerResource\Pages;

use App\Filament\Resources\ArticleContainerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArticleContainer extends EditRecord
{
    protected static string $resource = ArticleContainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
