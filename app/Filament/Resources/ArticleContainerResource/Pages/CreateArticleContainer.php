<?php

namespace App\Filament\Resources\ArticleContainerResource\Pages;

use App\Filament\Resources\ArticleContainerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticleContainer extends CreateRecord
{
    protected static string $resource = ArticleContainerResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
