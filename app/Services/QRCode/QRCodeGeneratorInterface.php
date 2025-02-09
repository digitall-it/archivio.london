<?php

namespace App\Services\QRCode;

/**
 * Interface QRCodeGeneratorInterface
 *
 * Provides a method for generating QR codes as base64 data URIs.
 */
interface QRCodeGeneratorInterface
{
    /**
     * Generate a QR code image in base64 data URI format.
     *
     * @param  string  $content  The content to encode in the QR code.
     * @param  int  $size  The size (in pixels) for the QR code.
     * @return string The QR code as a base64 encoded PNG image.
     */
    public function generateQRCode(string $content, int $size): string;
}
