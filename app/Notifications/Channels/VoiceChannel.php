<?php

namespace App\Notifications\Channels;

use App\Exceptions\VoiceMethodNotDefinedException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class VoiceChannel
{
    /**
     * Send the notification.
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

        // Get the message from the notification
        $message = $notification->toVoice($notifiable);

        // Generate a temporary filename
        $filename = md5($message) . '.wav';
        $path = 'audio/' . $filename;

        if (!Storage::exists($path)) {
            // Command to generate the audio file
            $generationProcess = new Process(['pico2wave', '-l', 'it-IT', '-w', $filename, $message]);
            $generationProcess->run();

            // Check if the command was successful
            if (!$generationProcess->isSuccessful()) {
                throw new ProcessFailedException($generationProcess);
            }

            // Check if the audio file was generated
            if (!file_exists($filename)) {
                throw new ProcessFailedException($generationProcess);
            }
        }

        // Command to play the audio file
        $playProcess = new Process(['aplay', $filename]);
        $playProcess->run();

        // Check if the command was successful
        if (!$playProcess->isSuccessful()) {
            throw new ProcessFailedException($playProcess);
        }
    }
}
