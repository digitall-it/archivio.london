<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class QrCodeScannedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $qrCode;

    public function __construct(string $qrCode)
    {
        $this->qrCode = $qrCode;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => "QR Code scansionato: {$this->qrCode}",
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "QR Code scansionato: {$this->qrCode}",
        ];
    }

    public function toFilamentDatabase($notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title('QR Code Scansionato')
            ->body("È stato scansionato un nuovo QR Code: {$this->qrCode}")
            ->success()
            ->icon('heroicon-o-qrcode')
            ->sendToDatabase($notifiable);
    }
}
