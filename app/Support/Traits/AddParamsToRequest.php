<?php

namespace App\Support\Traits;

use App\Support\Helper;
use Illuminate\Pagination\LengthAwarePaginator;

trait AddParamsToRequest
{
    public static function addQueryParamsToRequest($request)
    {
        $static = static::class;

        $request->merge([
            'orderBy' => $request->orderBy ?: $static::DEFAULT_ORDER_BY,
            'orderType' => $request->orderType ?: $static::DEFAULT_ORDER_TYPE,
            'paginationLimit' => $request->paginationLimit ?: $static::DEFAULT_PAGINATION_LIMIT,
            'page' => LengthAwarePaginator::resolveCurrentPage(), // used while paginating model items
        ]);

        Helper::addReversedSortingUrlToRequest($request);
    }
}
