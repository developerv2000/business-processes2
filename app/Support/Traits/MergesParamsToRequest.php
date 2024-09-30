<?php

namespace App\Support\Traits;

use App\Support\Helper;
use Illuminate\Http\Request;

/**
 * Trait MergesParamsToRequest
 *
 * This trait provides functionality for merging query parameters into a request object.
 *
 * @package App\Support\Traits
 */
trait MergesParamsToRequest
{
    /**
     * Merge default querying parameters into the given request object if misses.
     *
     * Used in index & trash pages for ordering, sorting & pagination.
     *
     * @param \Illuminate\Http\Request $request The request object to merge parameters into.
     * @return void
     */
    public static function mergeQueryingParamsToRequest(Request $request)
    {
        // Merge default querying parameters into the request
        $request->mergeIfMissing([
            'orderBy' => static::DEFAULT_ORDER_BY,
            'orderType' => static::DEFAULT_ORDER_TYPE,
            'paginationLimit' => static::DEFAULT_PAGINATION_LIMIT,
        ]);

        // Merge reversed sorting URL to the request
        Helper::mergeReversedSortingUrlToRequest($request);
    }

    /**
     * Merge export querying parameters into the given request object,
     * from the referer header's query of the incoming request.
     *
     * Used only on export.
     *
     * @param \Illuminate\Http\Request $request The request object to merge parameters into.
     * @return void
     */
    public static function mergeExportQueryingParamsToRequest(Request $request)
    {
        // Merge referer URL query parameters into the request
        $url = $request->header('referer');
        Helper::mergeUrlParamsToRequest($request, $url);

        // Also merge default querying parameters to escape querying errors
        static::mergeQueryingParamsToRequest($request);
    }
}
