<?php

namespace App\Listeners;

use App\Events\LabelPrintRequestedEvent;
use App\Models\Article;
use App\Models\ArticleContainer;
use App\Services\LabelPrinterService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleLabelPrintRequestListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(protected LabelPrinterService $labelPrinterService) {}

    public function handle(LabelPrintRequestedEvent $event): void
    {
        try {
            switch ($event->data['type']) {
                case 'article':
                    $article = Article::findOrFail($event->data['id']);
                    $this->labelPrinterService->printArticleLabel($article);
                    break;
                case 'container':
                    $container = ArticleContainer::findOrFail($event->data['id']);
                    $this->labelPrinterService->printContainerLabel($container, $event->data['mode']);
                    break;
                default:
                    Log::warning("Unknown label type: {$event->data['type']}");
                    break;
            }
        } catch (Exception $e) {
            Log::error('Label printing failed: '.$e->getMessage());
        }
    }
}
