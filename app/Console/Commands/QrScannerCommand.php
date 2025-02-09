<?php

namespace App\Console\Commands;

use App\Services\QrCodeScannerService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QrScannerCommand extends Command
{
    protected $signature = 'qrscanner {port} {--max-time=360} {--sleep=750}';
    protected $description = 'Start the QR Code scanner daemon on a serial USB port';

    public function handle()
    {
        $port = $this->argument('port');
        $maxTime = (int) $this->option('max-time');
        $sleepTime = (int) $this->option('sleep');

        try {
            $scanner = new QrCodeScannerService($port, $maxTime, $sleepTime);
            $scanner->start();
        } catch (Exception $e) {
            Log::error("Error in QR Scanner: " . $e->getMessage());
            $this->error($e->getMessage());
        }
    }
}
