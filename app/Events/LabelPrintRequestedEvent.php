<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LabelPrintRequestedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public array $data,
    ) {}
}
