<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $frontend_active;

    public static function group(): string
    {
        return 'general';
    }
}
