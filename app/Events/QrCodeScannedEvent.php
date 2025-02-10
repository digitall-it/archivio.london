<?php

namespace App\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class QrCodeScannedEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue;

    public function __construct(public string $qrCode) {}
}
