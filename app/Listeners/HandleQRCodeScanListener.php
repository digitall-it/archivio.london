<?php

namespace App\Listeners;

use App\Events\QrCodeScannedEvent;
use Illuminate\Support\Facades\Log;

class HandleQRCodeScanListener
{
    public function __construct()
    {
    }

    public function handle(QrCodeScannedEvent $event): void
    {
        Log::info("QR Code processed: " . $event->qrCode);
    }
}
