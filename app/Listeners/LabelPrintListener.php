<?php

namespace App\Listeners;

use App\Events\LabelPrintRequestedEvent;
use App\Models\ArticleContainer;
use App\Services\LabelPrinterService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LabelPrintListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(protected LabelPrinterService $labelPrinterService) {}

    public function handle(LabelPrintRequestedEvent $event): void
    {
        try {
            if ($event->data['type'] === 'container') {
                $container = ArticleContainer::find($event->data['id']);
                if ($container) {
                    $this->labelPrinterService->printContainerLabel($container, $event->data['mode']);
                } else {
                    Log::error("Article Container ID {$event->data['id']} not found.");
                }
            } else {
                Log::warning("Unknown label type: {$event->data['type']}");
            }
        } catch (\Exception $e) {
            Log::error('Label printing failed: '.$e->getMessage());
        }
    }
}
