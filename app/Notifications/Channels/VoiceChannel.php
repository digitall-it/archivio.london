<?php

namespace App\Notifications\Channels;

use App\Exceptions\VoiceMethodNotDefinedException;
use Illuminate\Notifications\Notification;
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
        $filename = tempnam(sys_get_temp_dir(), 'voice_') . '.wav';

        // Command to generate the audio file
        $process = new Process(['pico2wave', '-l', 'it-IT', '-w', $filename, $message]);
        $process->run();

        // Check if the command was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Check if the audio file was generated
        if (!file_exists($filename)) {
            throw new ProcessFailedException($process);
        }

        // Command to play the audio file
        $playProcess = new Process(['aplay', $filename]);
        $playProcess->run();

        // Check if the command was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Remove the temporary audio file
        unlink($filename);
    }
}
