<?php

namespace App\Filament\Resources\ArticleContainerResource\Pages;

use App\Filament\Resources\ArticleContainerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArticleContainers extends ListRecords
{
    protected static string $resource = ArticleContainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
