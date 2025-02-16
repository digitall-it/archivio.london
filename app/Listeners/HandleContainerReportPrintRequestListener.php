<?php

namespace App\Listeners;

use App\Events\ArticleContainerReportPrintRequestedEvent;
use App\Models\Article;
use App\Models\ArticleContainer;
use App\Services\ReceiptPrinterService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleContainerReportPrintRequestListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(protected ReceiptPrinterService $receiptPrinterService) {}

    public function handle(ArticleContainerReportPrintRequestedEvent $event): void
    {
        try {
            $articleContainer = ArticleContainer::findOrFail($event->articleContainerId);

            $articles = $event->includeChildren
                ? Article::whereIn('article_container_id', $articleContainer->descendantsAndSelf()->pluck('id'))->get()
                : $articleContainer->articles()->get();

            $this->receiptPrinterService->printContainerReport($articleContainer, $articles);
        } catch (Exception $e) {
            Log::error("Container report printing failed: {$e->getMessage()}");
        }
    }
}
