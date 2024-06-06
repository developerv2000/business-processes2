<?php

namespace App\Support\Abstracts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Abstract Class TemplatedModel
 *
 * This abstract class provides a base for models that need to define eager loading of relation counts
 * for performance reasons and implement a method to retrieve the usage count of the model.
 *
 * @package App\Support\Abstracts
 */
abstract class TemplatedModel extends Model
{
    use HasFactory;

    /**
     * Define the eager loading of relation counts for the model.
     *
     * This method must be implemented by subclasses to specify the relations
     * whose counts should be eager loaded for performance reasons.
     *
     * @return array The relations to be counted for the model.
     */
    abstract public function withCount(): array;

    /**
     * Get the usage count of the model.
     *
     * This method should be implemented by subclasses to provide the total usage count of the model.
     * It typically aggregates the counts of all (eager loaded) relevant relations.
     *
     * @return int The usage count of the model.
     */
    abstract public function getUsageCountAttribute(): int;
}
