<?php

namespace App\Support\Interfaces;

/**
 * HasTitle Model Interface
 *
 * Each model that uses App\Support\Traits\Commentable trait should implement this interface
 *
 * Displays title of commentable_type on comment create/edit pages
 *
 * @package App\Support\Interfaces
 */
interface HasTitle
{
    public function getTitle(): string;
}
