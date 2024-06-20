<?php

namespace App\Support\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface PreparesRecordsForExportInterface
 *
 * This interface defines methods for preparing Eloquent model records for export.
 */
interface PreparesRecordsForExportInterface
{
    /**
     * Prepare Eloquent model records for export.
     *
     * @param \Illuminate\Database\Eloquent\Collection $records The collection of Eloquent model records to prepare.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function prepareRecordsForExport(Collection $records);
}
