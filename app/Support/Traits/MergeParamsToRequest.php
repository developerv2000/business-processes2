<?php

namespace App\Support\Traits;

use App\Support\Helper;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Trait MergeParamsToRequest
 *
 * This trait provides functionality for merging query parameters into a request object.
 *
 * @package App\Traits
 */
trait MergeParamsToRequest
{
    /**
     * Merge query parameters into the given request object.
     *
     * @param \Illuminate\Http\Request $request The request object to merge parameters into.
     * @return void
     */
    public static function mergeQueryParamsToRequest($request)
    {
        $static = static::class;

        // Merge default query parameters into the request
        $request->merge([
            'orderBy' => $request->orderBy ?: $static::DEFAULT_ORDER_BY,
            'orderType' => $request->orderType ?: $static::DEFAULT_ORDER_TYPE,
            'paginationLimit' => $request->paginationLimit ?: $static::DEFAULT_PAGINATION_LIMIT,
            'page' => LengthAwarePaginator::resolveCurrentPage(),
        ]);

        // Merge reversed sorting URL to the request
        Helper::mergeReversedSortingUrlToRequest($request);
    }
}
