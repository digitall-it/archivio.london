<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class PrinterIOErrorEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return ['printer-errors'];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return ['message' => $this->message];
    }
}
