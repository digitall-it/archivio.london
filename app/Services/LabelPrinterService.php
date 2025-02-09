<?php

namespace App\Services;

use App\Events\PrinterIOErrorEvent;
use App\Exceptions\LabelPrinterException;
use App\Models\ArticleContainer;
use App\Layouts\ContainerLoadLabelLayout;
use App\Services\QRCode\QRCodeGeneratorInterface;
use Exception;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

/**
 * Class LabelPrinterService
 *
 * Handles the generation of a container load/unload label as a PDF and sends it to a label printer.
 */
class LabelPrinterService
{
    /**
     * @param ConfigRepository         $config          The configuration repository.
     * @param QRCodeGeneratorInterface $qrCodeGenerator The QR code generator.
     */
    public function __construct(
        protected ConfigRepository $config,
        protected QRCodeGeneratorInterface $qrCodeGenerator,
    ) {}

    /**
     * Print a container label (load/unload) for the given container.
     *
     * @param ArticleContainer $container The container model.
     * @param string           $mode      The mode (e.g. 'load' or 'unload').
     * @return bool True on success, false on failure.
     */
    public function printContainerLabel(ArticleContainer $container, string $mode): bool
    {
        try {
            // Generate the URL to be embedded in the QR code.
            $qrUrl = url("/qr/$mode/$container->id");
            // Generate the QR code image as a base64 data URI.
            $qrCodeImage = $this->qrCodeGenerator->generateQRCode($qrUrl, 150);

            // Prepare data for the label layout.
            $layoutData = [
                'containerName' => $container->name,
                'mode'          => $mode,
                'qrCodeImage'   => $qrCodeImage,
            ];

            // Use the default container load label layout.
            $layout      = new ContainerLoadLabelLayout();
            $paperWidth  = $layout::PAPER_WIDTH;   // in mm
            $paperHeight = $layout::PAPER_HEIGHT;  // in mm

            // Create a new mPDF instance with the specified paper dimensions.
            $mpdf = new Mpdf(['format' => [$paperWidth, $paperHeight]]);

            // Render the HTML layout.
            $html = $layout->render($layoutData);
            $mpdf->WriteHTML($html);

            // Save the PDF to a temporary file.
            $tempPdfPath = tempnam(sys_get_temp_dir(), 'label_') . '.pdf';
            $mpdf->Output($tempPdfPath, Destination::FILE);

            // Retrieve the printer transport method and printer name from configuration.
            $transport   = $this->config->get('printers.label.transport', 'exec');
            $printerName = $this->config->get('printers.label.name');

            if ($transport === 'exec') {
                // Use the lp command to print the PDF.
                $command = sprintf('lp -d %s %s', escapeshellarg($printerName), escapeshellarg($tempPdfPath));
                exec($command, $output, $result);
                $success = $result === 0;
            } elseif ($transport === 'curl') {
                // Use cURL to send the PDF file to CUPS.
                $cupsUrl     = "http://localhost:631/printers/" . urlencode($printerName);
                $fileContent = file_get_contents($tempPdfPath);

                $ch = curl_init($cupsUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/octet-stream",
                    "Content-Length: " . strlen($fileContent),
                ]);
                curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $success = ($httpCode >= 200 && $httpCode < 300);
            } else {
                throw new LabelPrinterException("Invalid printer transport method configured");
            }

            // Remove the temporary PDF file.
            unlink($tempPdfPath);

            return $success;
        } catch (Exception $e) {
            PrinterIOErrorEvent::dispatch($e->getMessage());
            return false;
        }
    }
}
