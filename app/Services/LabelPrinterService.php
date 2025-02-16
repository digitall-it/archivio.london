<?php

namespace App\Services;

use App\Exceptions\LabelPrinterException;
use App\Layouts\ArticleLabelLayout;
use App\Layouts\ContainerLoadLabelLayout;
use App\Models\Article;
use App\Models\ArticleContainer;
use App\Services\QRCode\QRCodeGeneratorInterface;
use Exception;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Facades\Log;
use Random\RandomException;

/**
 * Class LabelPrinterService
 *
 * Handles the printing of labels for articles and containers.
 */
class LabelPrinterService
{
    public function __construct(
        protected ConfigRepository $config,
        protected QRCodeGeneratorInterface $qrCodeGenerator,
    ) {}

    /**
     * Print an article label.
     */
    public function printArticleLabel(Article $article): bool
    {
        $layoutData = ['articleName' => $article->name, 'id' => $article->id];

        return $this->processLabel(new ArticleLabelLayout, $layoutData, "article_$article->id");
    }

    /**
     * Print a container label (load/unload).
     */
    public function printContainerLabel(ArticleContainer $container, string $mode): bool
    {
        $layoutData = ['containerName' => $container->name, 'mode' => $mode, 'id' => $container->id];

        return $this->processLabel(new ContainerLoadLabelLayout, $layoutData, "container_$container->id");
    }

    /**
     * Generates and processes the label.
     */
    protected function processLabel($layout, array $layoutData, string $filename): bool
    {
        try {
            $transport = $this->config->get('printers.label.transport', 'log');
            $printerName = $this->config->get('printers.label.name', 'Brother_Label_Printer');
            $tempPdfPath = $this->generatePdf($layout, $layoutData, $filename, $transport);

            return $this->sendToPrinter($tempPdfPath, $transport, $printerName);
        } catch (Exception $e) {
            Log::error('Label print error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Generates the PDF label.
     * @throws RandomException
     */
    protected function generatePdf($layout, array $layoutData, string $filename, string $transport): string
    {
        $timestamp = date('Ymd_His');
        $random = bin2hex(random_bytes(4));
        $tempPdfPath = $transport === 'log'
            ? $_SERVER['HOME']."/Desktop/label_{$filename}_{$timestamp}_$random.pdf"
            : tempnam(sys_get_temp_dir(), 'label_').'.pdf';

        $layout->generatePdf($layoutData, $tempPdfPath);

        return $tempPdfPath;
    }

    /**
     * Sends the generated label to the printer based on the selected transport.
     * @throws LabelPrinterException
     */
    protected function sendToPrinter(string $filePath, string $transport, string $printerName): bool
    {
        switch ($transport) {
            case 'log':
                Log::info('Label PDF generated at: '.realpath($filePath));

                return true;

            case 'exec':
                $command = sprintf('lp -d %s %s', escapeshellarg($printerName), escapeshellarg($filePath));
                exec($command, $output, $result);

                return $result === 0;

            case 'curl':
                return $this->sendToCups($filePath, $printerName);

            default:
                throw new LabelPrinterException('Invalid printer transport method configured');
        }
    }

    /**
     * Sends the label to CUPS via cURL.
     */
    protected function sendToCups(string $filePath, string $printerName): bool
    {
        $cupsUrl = 'http://localhost:631/printers/'.urlencode($printerName);
        $fileContent = file_get_contents($filePath);

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

        return $httpCode >= 200 && $httpCode < 300;
    }
}
