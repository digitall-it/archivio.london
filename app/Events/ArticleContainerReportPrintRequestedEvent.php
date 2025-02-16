<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticleContainerReportPrintRequestedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public bool $includeChildren, public int $articleContainerId) {}
}
