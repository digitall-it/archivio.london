<?php

namespace App\Services;

use App\Events\QrCodeScannedEvent;
use App\Exceptions\SerialIOException;
use App\Serial\ManagedSerialPort;
use Exception;
use Illuminate\Support\Facades\Log;
use lepiaf\SerialPort\Configure\TTYConfigure;
use lepiaf\SerialPort\Parser\SeparatorParser;

class QrCodeScannerService
{
    protected string $port;

    protected int $maxTime; // TTL in seconds

    protected int $readDelay; // Sleep time in microseconds

    protected int $readTimeout; // Timeout in seconds

    protected $logCallback;

    public function __construct(
        string $port,
        int $maxTime = 3600, // 1 hour TTL
        int $readDelay = 50000, // 50ms sleep
        int $readTimeout = 10, // 10s max wait time for data
        ?callable $logCallback = null
    ) {
        $this->port = $port;
        $this->maxTime = $maxTime;
        $this->readDelay = $readDelay;
        $this->readTimeout = $readTimeout;
        $this->logCallback = $logCallback;
    }

    /**
     * @throws SerialIOException
     */
    public function start(): void
    {
        try {
            $this->logMessage('info', "Attempting to open serial port: $this->port");

            // Passiamo readTimeout e readDelay
            $serialPort = new ManagedSerialPort(new SeparatorParser("\r"), new TTYConfigure, $this->readDelay, $this->readTimeout);
            $serialPort->open($this->port, 'r');

            $this->logMessage('info', "Serial port opened successfully: $this->port");

            $startTime = time();
            $this->logMessage('info', 'Entering QR code read loop...');

            while ((time() - $startTime) < $this->maxTime) {
                try {
                    $data = $serialPort->read();
                } catch (Exception $e) {
                    $this->logMessage('error', 'Error reading from serial port: '.$e->getMessage());
                    break;
                }

                if ($data === false) {
                    $this->logMessage('debug', 'Graceful exit from managed serial port read loop.');
                    break;
                }

                $qrCode = trim($data);
                $this->logMessage('info', 'QR Code scanned: '.$qrCode);
                event(new QrCodeScannedEvent($qrCode));

                $this->logMessage('debug', 'Waiting for next QR code scan...');
            }

            $this->logMessage('info', "Closing serial port: $this->port");
            $serialPort->close();
            $this->logMessage('info', 'Serial port closed successfully.');
        } catch (Exception $e) {
            $this->logMessage('error', 'Serial communication error: '.$e->getMessage());
            throw new SerialIOException('Error in serial IO communication');
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
