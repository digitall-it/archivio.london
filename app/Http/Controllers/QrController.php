<?php

namespace App\Http\Controllers;

use App\Events\ContainerQrEvent;
use App\Models\ArticleContainer;

/**
 * Class QrController
 *
 * Handles QR code related requests.
 */
class QrController extends Controller
{
    /**
     * Display a message indicating that the QR command has been received,
     * and emit an event for further processing.
     *
     * @param  string  $mode  The mode (e.g. 'load' or 'unload').
     * @param  ArticleContainer  $articleContainer  The container model (via implicit binding).
     * @return \Illuminate\View\View The view displaying the message.
     */
    public function show(string $mode, ArticleContainer $articleContainer)
    {
        $containerName = $articleContainer->name;
        $message = "Command received, container {$containerName} in {$mode} mode";

        // Emit the event for further processing.
        event(new ContainerQrEvent($articleContainer->id, $mode));

        // Return a view (resources/views/qr/show.blade.php) with the message.
        return view('qr.show', ['message' => $message]);
    }
}
