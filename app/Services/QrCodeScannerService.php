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
    protected $logCallback;

    public function __construct(
        string $port,
        int $maxTime = 360,
        int $sleepTime = 750,
        callable $logCallback = null
    ) {
        $this->port = $port;
        $this->maxTime = $maxTime;
        $this->sleepTime = $sleepTime * 1000; // Convert milliseconds to microseconds
        $this->logCallback = $logCallback;
    }

    /**
     * @throws SerialIOException
     */
    public function start(): void
    {
        try {
            $this->logMessage('info', "Attempting to open serial port: $this->port");

            $serialPort = new SerialPort(new SeparatorParser("\r"), new TTYConfigure());
            $serialPort->open($this->port, 'r');

            $this->logMessage('info', "Serial port opened successfully: $this->port");

            $startTime = time();
            $this->logMessage('info', "Entering QR code read loop...");

            while ((time() - $startTime) < $this->maxTime) {
                $data = $serialPort->read();

                if ($data === '') {
                    $this->logMessage('debug', "No data received from serial port.");
                    usleep($this->sleepTime);
                    continue;
                }

                $qrCode = trim($data);
                $this->logMessage('info', "QR Code scanned: " . $qrCode);
                event(new QrCodeScannedEvent($qrCode));

                $this->logMessage('debug', "Waiting for next QR code scan...");
                usleep($this->sleepTime); // Delay to reduce CPU load
            }

            $this->logMessage('info', "Closing serial port: $this->port");
            $serialPort->close();
            $this->logMessage('info', "Serial port closed successfully.");
        } catch (Exception $e) {
            $this->logMessage('error', "Serial communication error: " . $e->getMessage());
            throw new SerialIOException("Error in serial IO communication");
        }
    }

    private function logMessage(string $level, string $message): void
    {
        Log::{$level}($message);
        if (is_callable($this->logCallback)) {
            call_user_func($this->logCallback, $level, $message);
        }
    }
}
