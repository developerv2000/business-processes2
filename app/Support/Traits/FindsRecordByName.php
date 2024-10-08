<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Trait FindsRecordByName
 *
 * Provides a method to find a model record by its name attribute.
 *
 * @package App\Support\Traits\Model
 */
trait FindsRecordByName
{
    /**
     * Find a model record by its name.
     *
     * This method searches for a record where the 'name' attribute matches the given value.
     * If no record is found, it throws a ModelNotFoundException.
     *
     * @param string $name The name value to search for.
     * @return static The found model instance.
     *
     * @throws ModelNotFoundException If no record is found with the given name.
     */
    public static function findByName(string $name)
    {
        return self::where('name', $name)->first();
    }
}
