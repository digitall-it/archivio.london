<?php

namespace App\Services\QRCode;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Class EndroidQRCodeGenerator
 *
 * Implements QR code generation using the endroid/qr-code library.
 */
class EndroidQRCodeGenerator implements QRCodeGeneratorInterface
{
    /**
     * Generate a QR code image in base64 data URI format.
     *
     * @param string $content The content to encode.
     * @param int $size The size (in pixels) for the QR code.
     * @return string The QR code as a base64 encoded PNG image.
     * @throws ValidationException
     */
    public function generateQRCode(string $content, int $size): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $content,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        return $builder->build()->getDataUri();
    }
}
