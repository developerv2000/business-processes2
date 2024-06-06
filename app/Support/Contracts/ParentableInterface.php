<?php

namespace App\Support\Contracts;

/**
 * Parentalbe Model Interface
 *
 * Templated models with parent_id attribute should implement this interface
 *
 * @package App\Support\Contracts
 */
interface ParentableInterface
{
    public function scopeOnlyParents();
}
