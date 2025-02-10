<?php

namespace App\Listeners;

use App\Events\QrCodeScannedEvent;
use App\Models\User;
use App\Notifications\QrCodeScannedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleQRCodeScanListener implements ShouldQueue
{
    use InteractsWithQueue;

    // public function __construct() {}

    public function handle(QrCodeScannedEvent $event): void
    {
        Log::info('QR Code processed: '.$event->qrCode);

        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new QrCodeScannedNotification($event->qrCode));
        }
    }
}
