<?php

namespace App\Services;

use App\Exceptions\LabelPrinterException;
use App\Layouts\ContainerLoadLabelLayout;
use App\Models\ArticleContainer;
use App\Services\QRCode\QRCodeGeneratorInterface;
use Exception;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Facades\Log;

/**
 * Class LabelPrinterService
 *
 * Handles the printing of container labels.
 */
class LabelPrinterService
{
    public function __construct(
        protected ConfigRepository $config,
        protected QRCodeGeneratorInterface $qrCodeGenerator,
    ) {}

    /**
     * Print a container label (load/unload) for the given container.
     *
     * @param  ArticleContainer  $container  The container model.
     * @param  string  $mode  The mode (e.g. 'load' or 'unload').
     * @return bool True on success, false on failure.
     */
    public function printContainerLabel(ArticleContainer $container, string $mode): bool
    {
        try {
            // Prepare layout data
            $layoutData = [
                'containerName' => $container->name,
                'mode' => $mode,
                'id' => $container->id,
            ];

            $transport = $this->config->get('printers.label.transport', 'log');
            $printerName = $this->config->get('printers.label.name', 'Brother_Label_Printer');

            // Crea il file PDF direttamente dal layout
            if ($transport === 'log') {
                $timestamp = date('Ymd_His');
                $tempPdfPath = $_SERVER['HOME'] . "/Desktop/label_{$container->id}_{$timestamp}.pdf";
            } else {
            $tempPdfPath = tempnam(sys_get_temp_dir(), 'label_').'.pdf';
            }
            $layout = new ContainerLoadLabelLayout;
            $layout->generatePdf($layoutData, $tempPdfPath);

            // Controlla il metodo di trasporto



            switch ($transport) {
                case 'log':
                    // $tempPdfPath is the path to the desktop, so we can open it manually.

                    Log::info('Label PDF generated at: '.realpath($tempPdfPath));
                    $success = true;
                    break;

                case 'exec':
                    // Use the lp command to print the PDF.
                    $command = sprintf('lp -d %s %s', escapeshellarg($printerName), escapeshellarg($tempPdfPath));
                    exec($command, $output, $result);
                    $success = $result === 0;
                    break;

                case 'curl':
                    // Use cURL to send the PDF file to CUPS.
                    $cupsUrl = 'http://localhost:631/printers/'.urlencode($printerName);
                    $fileContent = file_get_contents($tempPdfPath);

                    $ch = curl_init($cupsUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/octet-stream',
                        'Content-Length: '.strlen($fileContent),
                    ]);
                    curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    $success = ($httpCode >= 200 && $httpCode < 300);
                    break;

                default:
                    throw new LabelPrinterException('Invalid printer transport method configured');
            }

            return $success;
        } catch (Exception $e) {
            Log::error('Label print error: '.$e->getMessage());

            return false;
        }
    }
}
