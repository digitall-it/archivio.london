<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Label Printer Configuration
    |--------------------------------------------------------------------------
    |
    | The following configuration is used for printing labels. You can choose
    | the transport method ('exec' or 'curl') and set the printer name as
    | defined in your CUPS configuration.
    |
    */

    'name'      => env('LABEL_PRINTER_NAME', 'Brother_Label_Printer'),
    'transport' => env('LABEL_PRINTER_TRANSPORT', 'exec'),
];
