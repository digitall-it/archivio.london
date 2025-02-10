<?php

namespace App\Console\Commands;

use App\Services\QrCodeScannerService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QrScannerCommand extends Command
{
    protected $signature = 'qrscanner {port} {--max-time=3600} {--read-delay=50000} {--read-timeout=60}';

    protected $description = 'Start the QR Code scanner daemon on a serial USB port';

    public function handle(): void
    {
        $port = $this->argument('port');
        $maxTime = (int) $this->option('max-time');
        $readDelay = (int) $this->option('read-delay');
        $readTimeout = (int) $this->option('read-timeout');

        try {
            $scanner = new QrCodeScannerService($port, $maxTime, $readDelay, $readTimeout, function ($level, $message) {
                $this->{$level}($message);
            });
            $scanner->start();
        } catch (Exception $e) {
            Log::error('Error in QR Scanner: '.$e->getMessage());
            $this->error($e->getMessage());
        }
    }
}
