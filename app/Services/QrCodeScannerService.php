<?php

namespace App\Services;

use App\Events\QrCodeScannedEvent;
use App\Exceptions\SerialIOException;
use Exception;
use Illuminate\Support\Facades\Log;
use Lepiaf\SerialPort\SerialPort;
use Lepiaf\SerialPort\Parser\SeparatorParser;

class QrCodeScannerService
{
    protected string $port;
    protected int $maxTime;
    protected int $sleepTime;
    protected ?SerialPort $serial = null;

    public function __construct(string $port, int $maxTime = 360, int $sleepTime = 750)
    {
        $this->port = $port;
        $this->maxTime = $maxTime;
        $this->sleepTime = $sleepTime * 1000; // Convert milliseconds to microseconds
    }

    /**
     * @throws SerialIOException
     */
    public function start(): void
    {
        try {
            // Initialize serial communication with SeparatorParser for CRLF
            $this->serial = new SerialPort(new SeparatorParser("\r\n"));
            $this->serial->open($this->port, 'r'); // Open in read-only mode

            $startTime = time();

            while ((time() - $startTime) < $this->maxTime) {
                $qrCode = $this->serial->read();

                if (is_string($qrCode) && $qrCode !== '') {
                    Log::info("QR Code scanned: " . trim($qrCode));
                    event(new QrCodeScannedEvent(trim($qrCode)));
                }

                usleep($this->sleepTime); // Delay to reduce CPU load
            }

            $this->serial->close();
        } catch (Exception $e) {
            Log::error("Serial communication error: " . $e->getMessage());
            throw new SerialIOException("Error in serial IO communication");
        }
    }
}
