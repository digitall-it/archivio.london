<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ContainerQrEvent
 *
 * Event emitted when a QR code command is received.
 */
class ContainerQrEvent
{
    use Dispatchable, SerializesModels;

    /** @var int The container ID */
    public int $containerId;

    /** @var string The mode (e.g. 'load' or 'unload') */
    public string $mode;

    /**
     * Create a new event instance.
     *
     * @param  int  $containerId  The container ID.
     * @param  string  $mode  The mode.
     */
    public function __construct(int $containerId, string $mode)
    {
        $this->containerId = $containerId;
        $this->mode = $mode;
    }
}
