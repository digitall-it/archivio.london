<?php

namespace App\Notifications\Channels;

use App\Exceptions\VoiceFileNotCreatedException;
use App\Exceptions\VoiceMethodNotDefinedException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
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
     * @throws VoiceMethodNotDefinedException|VoiceFileNotCreatedException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toVoice')) {
            throw new VoiceMethodNotDefinedException();
        }

        $message = $notification->toVoice($notifiable);

        $filename = md5($message) . '.wav';
        $path = 'audio/' . $filename;

        if (!Storage::exists('audio')) {
            Storage::makeDirectory('audio');
        }

        if (!Storage::exists($path)) {
            $fullPath = storage_path('app/' . $path);

            $generationProcess = new Process(['pico2wave', '-l', 'it-IT', '-w', $fullPath, $message]);
            $generationProcess->run();

            if (!$generationProcess->isSuccessful()) {
                throw new ProcessFailedException($generationProcess);
            }

            if (!file_exists($fullPath)) {
                throw new VoiceFileNotCreatedException("Il file audio non è stato creato: $fullPath");
            }
        }

        $playProcess = new Process(['aplay', storage_path('app/' . $path)]);
        $playProcess->run();

        if (!$playProcess->isSuccessful()) {
            throw new ProcessFailedException($playProcess);
        }
    }
}
