<?php

namespace App\Layouts;

use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;

/**
 * Class ContainerLoadLabelLayout
 *
 * Implements a layout for a container load label with fixed dimensions.
 */
class ContainerLoadLabelLayout implements LabelLayoutInterface
{
    // Paper dimensions in millimeters
    public const PAPER_WIDTH = 29;

    public const PAPER_HEIGHT = 90;

    /**
     * Generate the label PDF and save it to the specified file.
     *
     * @param  array  $data  The layout data.
     * @param  string  $filePath  The absolute path where the PDF should be saved.
     *
     * @throws MpdfException
     */
    public function generatePdf(array $data, string $filePath): void
    {
        // Percorso della cartella font
        $fontDir = resource_path('fonts');

        // Configura mPDF con il supporto per i font personalizzati
        $defaultConfig = (new ConfigVariables)->getDefaults();
        $fontDirs = array_merge($defaultConfig['fontDir'], [$fontDir]);

        $defaultFontConfig = (new FontVariables)->getDefaults();
        $fontData = $defaultFontConfig['fontdata'] + [
            'dosis' => [
                'R' => 'Dosis-VariableFont_wght.ttf',
            ],
        ];

        // Creiamo il PDF con margini a 0 e impostiamo il font custom
        $mpdf = new Mpdf([
            'format' => [self::PAPER_WIDTH, self::PAPER_HEIGHT],
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
            'fontDir' => $fontDirs,
            'fontdata' => $fontData,
            'default_font' => 'dosis',
        ]);
        // $mpdf->AddPage('L'); // Set landscape mode

        // Renderizza il template Laravel
        $html = View::make('pdf.container_label', $data)->render();
        $mpdf->WriteHTML($html);

        // Salva il PDF nel percorso specificato
        $mpdf->Output($filePath, Destination::FILE);
    }
}
