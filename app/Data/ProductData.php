<?php

namespace App\Data;

use Spatie\LaravelData\Data;

/**
 * Class ProductData
 *
 * Represents a product with a name and an optional quantity.
 */
class ProductData extends Data
{
    /** @var string Product name */
    public string $name;

    /** @var int|null Product quantity (optional) */
    public ?int $quantity = null;
}
