<?php

namespace App\Layouts;

/**
 * Interface LabelLayoutInterface
 *
 * Provides a method for generating HTML layouts for label printing
 * and creating a PDF from them.
 */
interface LabelLayoutInterface
{
    /**
     * Generate the label PDF and save it to the specified file.
     *
     * @param  array  $data  The layout data.
     * @param  string  $filePath  The absolute path where the PDF should be saved.
     */
    public function generatePdf(array $data, string $filePath): void;
}
