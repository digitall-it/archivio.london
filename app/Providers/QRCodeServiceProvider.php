<?php

namespace App\Providers;

use App\Services\QRCode\EndroidQRCodeGenerator;
use App\Services\QRCode\QRCodeGeneratorInterface;
use Illuminate\Support\ServiceProvider;

class QRCodeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(QRCodeGeneratorInterface::class, EndroidQRCodeGenerator::class);
    }

//    public function boot(): void
//    {
//    }
}
