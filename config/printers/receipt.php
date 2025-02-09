<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Receipt Printer Configuration
    |--------------------------------------------------------------------------
    |
    | The following configuration is used for printing receipts via the
    | ESC/POS printer using mike42/escpos-php.
    |
    */

    'host' => env('RECEIPT_PRINTER_HOST', '192.168.1.100'),
];
