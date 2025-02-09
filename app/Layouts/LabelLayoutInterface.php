<?php

namespace App\Layouts;

/**
 * Interface LabelLayoutInterface
 *
 * Provides an interface for generating HTML layouts for label printing.
 */
interface LabelLayoutInterface
{
    /**
     * Render the label layout as HTML.
     *
     * @param array $data An array containing the data required for the layout.
     *                    Expected keys: 'containerName', 'mode', 'qrCodeImage'.
     * @return string The HTML string for the label.
     */
    public function render(array $data): string;
}
