<?php

namespace App\Support\Traits;

use App\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Trait MergeParamsToRequest
 *
 * This trait provides functionality for merging query parameters into a request object.
 *
 * @package App\Traits
 */
trait MergesParamsToRequest
{
    /**
     * Merge query parameters into the given request object.
     *
     * Used in index & trash pages for ordering, sorting & pagination.
     *
     * @param \Illuminate\Http\Request $request The request object to merge parameters into.
     * @return void
     */
    public static function mergeQueryParamsToRequest(Request $request)
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

    /**
     * Merge query parameters into the request and
     * Merge export parameters into the given request object from requests previous url query.
     *
     * Used only on export.
     *
     * @param \Illuminate\Http\Request $request The request object to merge parameters into.
     * @return void
     */
    public static function mergeExportParamsToRequest(Request $request)
    {
        $static = static::class;
        $static::mergeQueryParamsToRequest($request);

        $url = $request->input('previous_url');
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $queryParams);

        $request->merge($queryParams);
    }
}
