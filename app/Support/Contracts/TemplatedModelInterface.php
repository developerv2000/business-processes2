<?php

namespace App\Support\Contracts;

/**
 * Usage Countable Model Interface
 *
 * All templated models should implement this interface
 *
 * @package App\Support\Contracts
 */
interface TemplatedModelInterface
{
    /**
     * Get the usage count of the model.
     *
     * Must eager load relations count for performance reasons!
     * Used while deleting records and displaying usage count.
     *
     * @return int The usage count of the model.
     */
    public function getUsageCountAttribute(): int;
}
