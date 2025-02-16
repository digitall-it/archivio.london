<?php

namespace App\Filament\Resources\ArticleContainerResource\Pages;

use App\Filament\Resources\ArticleContainerResource;
use App\Models\ArticleContainer;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditArticleContainer extends EditRecord
{
    protected static string $resource = ArticleContainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printReport')
                ->label('Stampa Report Contenuti')
                ->icon('heroicon-o-receipt-tax')
                ->color('gray')
                ->form([
                    Checkbox::make('includeChildren')
                        ->label('Includi contenitori figli')
                        ->default(true),
                ])
                ->action(function (ArticleContainer $record, array $data) {
                    $record->printContent($data['includeChildren']);

                    Notification::make()
                        ->title('Richiesta di stampa inviata!')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Stampa report contenuti'),
            Action::make('printLabels')
                ->label('Stampa Etichette')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->form([
                    CheckboxList::make('modes')
                        ->label('Seleziona modalità di stampa')
                        ->options([
                            'load' => 'Carico',
                            'unload' => 'Scarico',
                        ])
                        ->default(['load', 'unload']) // ✅ Entrambe selezionate di default
                        ->columns(1), // ✅ Le checkbox saranno una sotto l'altra
                ])
                ->action(function (ArticleContainer $record, array $data) {
                    $record->printLabels($data['modes']);

                    Notification::make()
                        ->title('Richiesta di stampa inviata!')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Stampa etichette articolo'),

            DeleteAction::make(),
        ];
    }
}
