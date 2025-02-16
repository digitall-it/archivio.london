<?php

namespace App\Notifications\Channels;

use App\Exceptions\VoiceFileNotCreatedException;
use App\Exceptions\VoiceMethodNotDefinedException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class VoiceChannel
{
    /**
     * Invia la notifica.
     *
     * @throws VoiceMethodNotDefinedException|VoiceFileNotCreatedException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toVoice')) {
            throw new VoiceMethodNotDefinedException;
        }

        $message = $notification->toVoice($notifiable);

        $filename = md5($message).'.wav';
        $path = 'audio/'.$filename;

        if (! Storage::exists('audio')) {
            Storage::makeDirectory('audio');
        }

        $fullPath = storage_path('app/'.$path);

        if (! Storage::exists($path)) {

            $generationProcess = new Process(['pico2wave', '-l', 'it-IT', '-w', $fullPath, $message]);
            $generationProcess->run();

            if (! $generationProcess->isSuccessful()) {
                throw new ProcessFailedException($generationProcess);
            }
            Log::info("Audio file generated for message \"$message\" at $fullPath");
        }

        if (! Storage::exists($path)) {
            throw new VoiceFileNotCreatedException("Il file audio non è stato creato: $fullPath");
        }

        $playProcess = new Process(['aplay', storage_path('app/'.$path)]);
        $playProcess->run();

        if (! $playProcess->isSuccessful()) {
            throw new ProcessFailedException($playProcess);
        }

        Log::info("Audio file played for message \"$message\"");
    }
}
