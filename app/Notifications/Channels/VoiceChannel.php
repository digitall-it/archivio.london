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
     * Sends the notification as a voice message.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     * @throws VoiceMethodNotDefinedException
     * @throws VoiceFileNotCreatedException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        // Ensure the notification has the 'toVoice' method
        if (!method_exists($notification, 'toVoice')) {
            throw new VoiceMethodNotDefinedException('The toVoice method is not defined in the notification.');
        }

        // Get the message from the notification
        $message = $notification->toVoice($notifiable);

        // Generate a unique filename based on the message hash
        $audioFilename = md5($message) . '.wav';
        $audioDirectory = 'audio';
        $storageRelativePath = $audioDirectory . '/' . $audioFilename; // Relative path in Laravel storage
        $fullFilePath = storage_path('app/' . $storageRelativePath); // Absolute file path in filesystem

        // Ensure the storage directory for audio files exists
        if (!Storage::exists($audioDirectory)) {
            Storage::makeDirectory($audioDirectory);
        }

        // Generate the audio file only if it does not already exist
        if (!file_exists($fullFilePath)) {
            $command = sprintf(
                'pico2wave -l it-IT -w %s "%s"',
                escapeshellarg($fullFilePath),
                addslashes($message)
            );

            $generationProcess = Process::fromShellCommandline($command);
            $generationProcess->run();

            // Ensure the command executed successfully
            if (!$generationProcess->isSuccessful()) {
                throw new ProcessFailedException($generationProcess);
            }

            Log::info("Audio file generated for message \"$message\" at $fullFilePath");
        }

        // Ensure the audio file was actually created
        if (!file_exists($fullFilePath)) {
            throw new VoiceFileNotCreatedException("The audio file was not created: $fullFilePath");
        }

        // Play the audio file using 'aplay'
        $playCommand = sprintf('aplay %s', escapeshellarg($fullFilePath));
        $playProcess = Process::fromShellCommandline($playCommand);
        $playProcess->run();

        if (!$playProcess->isSuccessful()) {
            throw new ProcessFailedException($playProcess);
        }

        Log::info("Audio file played for message \"$message\"");
    }
}
