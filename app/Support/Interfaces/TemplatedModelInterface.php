<?php

namespace App\Support\Interfaces;

/**
 * Usage Countable Model Interface
 *
 * All templated models should implement this interface
 *
 * @package App\Support\Interfaces
 */
interface TemplatedModelInterface
{
    /**
     * Get the usage count of the model.
     *
     * Used while deleting records and displaying usage count.
     *
     * @return int The usage count of the model.
     */
    public function getUsageCountAttribute(): int;
}
