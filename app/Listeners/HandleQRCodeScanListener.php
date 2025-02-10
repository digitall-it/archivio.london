<?php

namespace App\Listeners;

use App\Events\QrCodeScannedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class HandleQRCodeScanListener implements ShouldQueue
{
    use \Illuminate\Queue\InteractsWithQueue;

    public function __construct() {}

    public function handle(QrCodeScannedEvent $event): void
    {
        Log::info('QR Code processed: '.$event->qrCode);
    }
}
