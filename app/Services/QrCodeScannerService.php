<?php

namespace App\Services;

use App\Events\QrCodeScannedEvent;
use App\Exceptions\SerialIOException;
use Exception;
use Illuminate\Support\Facades\Log;
use lepiaf\SerialPort\SerialPort;
use lepiaf\SerialPort\Parser\SeparatorParser;
use lepiaf\SerialPort\Configure\TTYConfigure;

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
            $serialPort = new SerialPort(new SeparatorParser(), new TTYConfigure());
            $serialPort->open($this->port, 'r');

            $startTime = time();

            while (
                ((time() - $startTime) < $this->maxTime) && $data = $serialPort->read()) {

                if (is_string($data) && $data !== '') {
                    Log::info("QR Code scanned: " . trim($data));
                    event(new QrCodeScannedEvent(trim($data)));
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
