<?php

namespace App\Serial;

use lepiaf\SerialPort\Configure\ConfigureInterface;
use lepiaf\SerialPort\Configure\TTYConfigure;
use lepiaf\SerialPort\Exception\DeviceNotAvailable;
use lepiaf\SerialPort\Exception\DeviceNotFound;
use lepiaf\SerialPort\Exception\DeviceNotOpened;
use lepiaf\SerialPort\Exception\WriteNotAllowed;
use lepiaf\SerialPort\Parser\ParserInterface;
use lepiaf\SerialPort\Parser\SeparatorParser;

/**
 * ManageSerialPort to handle managed serial connection easily with PHP
 * Suitable for Arduino communication
 *
 * @author Giancarlo Di Massa <giancarlo@digitall.it>
 * @author Thierry Thuon <lepiaf@users.noreply.github.com>
 * @copyright MIT
 */
class ManagedSerialPort
{
    /**
     * File descriptor
     *
     * @var resource
     */
    private $fd = false;

    private ?ParserInterface $parser;

    private ?ConfigureInterface $configure;

    private int $readDelay; // Sleep time in microseconds (usleep)

    private int $readTimeout; // Timeout in seconds

    public function __construct(?ParserInterface $parser = null, ?ConfigureInterface $configure = null, int $readDelay = 50000, int $readTimeout = 10)
    {
        $this->parser = $parser;
        $this->configure = $configure;
        $this->readDelay = $readDelay;
        $this->readTimeout = $readTimeout;
    }

    /**
     * Open serial connection
     *
     * @param  string  $device  path to device
     * @param  string  $mode  fopen mode
     *
     * @throws DeviceNotAvailable|DeviceNotFound
     */
    public function open(string $device, string $mode = 'w+b'): bool
    {
        if (file_exists($device) === false) {
            throw new DeviceNotFound;
        }

        $this->getConfigure()->configure($device);
        $this->fd = fopen($device, $mode);

        if ($this->fd !== false) {
            stream_set_blocking($this->fd, false);

            return true;
        }

        unset($this->fd);
        throw new DeviceNotAvailable($device);
    }

    /**
     * Write data into serial port line
     *
     *
     * @return int length of byte written
     *
     * @throws WriteNotAllowed|DeviceNotOpened
     */
    public function write(string $data): int
    {
        $this->ensureDeviceOpen();

        $dataWritten = fwrite($this->fd, $data);
        if ($dataWritten !== false) {
            fflush($this->fd);

            return $dataWritten;
        }

        throw new WriteNotAllowed;
    }

    /**
     * Read data byte per byte until separator found
     */
    public function read(): false|string
    {
        $this->ensureDeviceOpen();

        $chars = [];

        $startTime = time();

        do {
            if ((time() - $startTime) >= $this->readTimeout) {
                return false;
            }
            $char = fread($this->fd, 1);
            if ($char === '') {
                usleep($this->readDelay);

                continue;
            }
            $chars[] = $char;
        } while ($char !== $this->getParser()->getSeparator());

        return $this->getParser()->parse($chars);
    }

    /**
     * Close serial connection
     *
     * @return bool return true on success
     *
     * @throws DeviceNotOpened
     */
    public function close(): bool
    {
        $this->ensureDeviceOpen();

        $hasCloseFd = fclose($this->fd);
        $this->fd = false;

        return $hasCloseFd;
    }

    /**
     * Configure serial line
     */
    private function getConfigure(): ConfigureInterface|TTYConfigure
    {
        if ($this->configure === null) {
            $this->configure = new TTYConfigure;
        }

        return $this->configure;
    }

    /**
     * Get parser, if not defined, return new line parser by default
     */
    private function getParser(): SeparatorParser|ParserInterface
    {
        if ($this->parser === null) {
            $this->parser = new SeparatorParser;
        }

        return $this->parser;
    }

    /**
     * @throws DeviceNotOpened
     */
    private function ensureDeviceOpen(): void
    {
        if (! $this->fd) {
            throw new DeviceNotOpened;
        }
    }
}
