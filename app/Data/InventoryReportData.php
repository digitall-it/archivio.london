<?php

namespace App\Data;

use Spatie\LaravelData\Data;

/**
 * Class InventoryReportData
 *
 * Represents an inventory report consisting of a container and its products.
 */
class InventoryReportData extends Data
{
    /** @var ContainerData The container information */
    public ContainerData $container;

    /**
     * @var ProductData[] List of products contained
     */
    public array $products;
}
