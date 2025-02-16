<?php

namespace App\Exceptions;

use Exception;

class VoiceMethodNotDefinedException extends Exception
{
    protected $message = 'The notification does not implement the toVoice method required for the voice channel.';
}
