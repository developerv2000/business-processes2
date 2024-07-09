<?php

namespace App\Support\Abstracts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * All models with 'usage_count' attribute should implement this interface
 *
 * @package App\Support\Abstracts
 */
abstract class UsageCountableModel extends Model
{
    use HasFactory;

    /**
     * Recalculate the usage_count attribute for this model instance.
     *
     * @return void
     */
    abstract public function recalculateUsageCount(): void;

    /**
     * Recalculate the usage_count attributes for all records of this model.
     *
     * @return void
     */
    public static function recalculateAllUsageCounts(): void
    {
        static::query()->each(function ($instance) {
            $instance->recalculateUsageCount();
        });
    }
}
