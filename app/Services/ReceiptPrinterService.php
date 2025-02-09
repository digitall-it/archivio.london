<?php

namespace App\Services;

use App\Data\InventoryReportData;
use Exception;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

/**
 * Class ReceiptPrinterService
 *
 * Handles printing reports to the receipt (ESC/POS) printer.
 */
class ReceiptPrinterService
{
    /**
     * @param  ConfigRepository  $config  The configuration repository.
     */
    public function __construct(
        protected ConfigRepository $config,
    ) {}

    /**
     * Print an inventory report on the receipt printer.
     *
     * @param  InventoryReportData  $reportData  The inventory report data.
     * @return bool True on success, false on failure.
     */
    public function printInventoryReport(InventoryReportData $reportData): bool
    {
        try {
            // Retrieve printer host from configuration
            $printerHost = $this->config->get('printers.receipt.host');

            // Create a network connector for the ESC/POS printer
            $connector = new NetworkPrintConnector($printerHost);
            $printer = new Printer($connector);

            // Load logo from assets (the logo is at public/images/logo.svg)
            // NOTE: mike42/escpos-php may not support SVG directly.
            // You may need to convert the SVG to a supported image format.
            $logoPath = public_path('images/logo.svg');
            // For demonstration, we simply print the asset URL.
            $printer->text(asset('images/logo.svg')."\n");

            // Print container title in bold
            $printer->setEmphasis(true);
            $printer->text($reportData->container->name."\n");
            $printer->setEmphasis(false);

            // Print each product line: name and (optional) quantity.
            foreach ($reportData->products as $product) {
                $line = $product->name;
                if ($product->quantity !== null) {
                    $line .= ' ('.$product->quantity.')';
                }
                $printer->text($line."\n");
            }

            // Cut the paper and close the connection.
            $printer->cut();
            $printer->close();

            return true;
        } catch (Exception $e) {
            // TODO: Emit an IO error event for the receipt printer failure.
            return false;
        }
    }
}
