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
     * Implemented model must eager load relations count for performance reasons! (Not done yet!)
     * It typically aggregates the counts of all (eager loaded) relevant relations.
     *
     * @return int The usage count of the model.
     */
    public function getUsageCountAttribute(): int;
}
