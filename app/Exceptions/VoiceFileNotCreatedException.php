<?php

namespace App\Exceptions;

use Exception;

class VoiceFileNotCreatedException extends Exception
{
    protected $message = 'The voice file could not be created.';
}
