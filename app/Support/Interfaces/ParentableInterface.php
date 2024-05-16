<?php

namespace App\Support\Interfaces;

/**
 * Parentalbe Model Interface
 *
 * Templated models with parent_id attribute should implement this interface
 *
 * @package App\Support\Interfaces
 */
interface ParentableInterface
{
    public function scopeOnlyParents();
}
