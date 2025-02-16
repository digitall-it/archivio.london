<?php

namespace App\Notifications\Channels;

use App\Exceptions\VoiceMethodNotDefinedException;
use Illuminate\Notifications\Notification;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class VoiceChannel
{
    /**
     * Invia la notifica.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     * @throws VoiceMethodNotDefinedException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toVoice')) {
            throw new VoiceMethodNotDefinedException();
        }

        // Ottieni il messaggio dalla notifica
        $message = $notification->toVoice($notifiable);

        // Genera un nome di file temporaneo
        $filename = tempnam(sys_get_temp_dir(), 'voice_') . '.wav';

        // Comando per generare il file audio
        $process = new Process(['pico2wave', '-l', 'it-IT', '-w', $filename, $message]);
        $process->run();

        // Verifica se il comando ha avuto successo
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Comando per riprodurre il file audio
        $playProcess = new Process(['aplay', $filename]);
        $playProcess->run();

        // Rimuovi il file audio temporaneo
        unlink($filename);
    }
}
