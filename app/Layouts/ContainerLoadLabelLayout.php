<?php

namespace App\Layouts;

/**
 * Class ContainerLoadLabelLayout
 *
 * Implements a layout for a container load label with fixed dimensions.
 */
class ContainerLoadLabelLayout implements LabelLayoutInterface
{
    // Paper dimensions in millimeters.
    public const PAPER_WIDTH  = 29;
    public const PAPER_HEIGHT = 90;

    /**
     * Render the container load label as HTML.
     *
     * @param array $data Expected keys: 'containerName', 'mode', 'qrCodeImage'
     * @return string The HTML content for the PDF.
     */
    public function render(array $data): string
    {
        $containerName = $data['containerName'] ?? '';
        $mode          = $data['mode'] ?? '';
        $qrCodeImage   = $data['qrCodeImage'] ?? '';

        // The layout uses inline styles. The QR code is placed at the top,
        // and the container name and mode (e.g., "carico" or "scarico") are
        // shown at the bottom, rotated for vertical appearance.
        $html = '
        <html>
        <head>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    font-family: sans-serif;
                }
                .label-container {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    text-align: center;
                }
                .qr-code {
                    flex: 1;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .label-text {
                    flex: 0 0 auto;
                    /* Rotate text 90 degrees for vertical display */
                    transform: rotate(90deg);
                    transform-origin: center;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class="label-container">
                <div class="qr-code">
                    <img src="' . $qrCodeImage . '" alt="QR Code" style="max-width:100%; max-height:100%;">
                </div>
                <div class="label-text">
                    <div>' . htmlspecialchars($containerName) . '</div>
                    <div>' . htmlspecialchars($mode) . '</div>
                </div>
            </div>
        </body>
        </html>
        ';

        return $html;
    }
}
