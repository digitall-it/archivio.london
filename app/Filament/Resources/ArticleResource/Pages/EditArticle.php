<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Models\Article;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('printLabel')
                ->label('Stampa Etichetta')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function (Article $record) {
                    $record->printLabel();
                    Notification::make()
                        ->title('Richiesta di stampa inviata!')
                        ->success()
                        ->send();
                }
                )
                ->requiresConfirmation()
                ->modalHeading('Stampa etichetta articolo'),
            DeleteAction::make(),
        ];
    }
}
