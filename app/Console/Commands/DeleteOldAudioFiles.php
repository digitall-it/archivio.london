<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOldAudioFiles extends Command
{
    protected $signature = 'audio:clean {days=30 : Numero di giorni dopo i quali i file saranno eliminati}';

    protected $description = 'Elimina i file audio più vecchi di un certo numero di giorni';

    public function handle(): void
    {
        $days = $this->argument('days');
        $files = Storage::files('audio');

        $now = Carbon::now();

        foreach ($files as $file) {
            if ($now->diffInDays(Carbon::createFromTimestamp(Storage::lastModified($file))) >= $days) {
                Storage::delete($file);
                $this->info("Eliminato: $file");
            }
        }

        $this->info('Pulizia completata.');
    }
}
