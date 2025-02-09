<?php

namespace App\Data;

use Spatie\LaravelData\Data;

/**
 * Class ContainerData
 *
 * Represents a container with a name.
 */
class ContainerData extends Data
{
    /** @var string Container name */
    public string $name;
}
