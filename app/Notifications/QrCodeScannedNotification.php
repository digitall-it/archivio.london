<?php

namespace App\Notifications;

use App\Notifications\Channels\VoiceChannel;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

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
        return ['database', 'broadcast', VoiceChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('QR Code Scansionato')
            ->body("È stato scansionato un nuovo QR Code: $this->qrCode")
            ->success()
            ->icon('heroicon-o-qr-code')
            ->iconColor('success')
            ->getDatabaseMessage();
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('QR Code Scansionato')
            ->body("È stato scansionato un nuovo QR Code: $this->qrCode")
            ->success()
            ->persistent()
            ->icon('heroicon-o-qr-code')
            ->iconColor('success')
            ->getBroadcastMessage();
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "QR Code scansionato: $this->qrCode",
        ];
    }

    public function toVoice($notifiable): string
    {
        return 'Scansionato un nuovo codice QR';
    }
}
