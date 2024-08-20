<?php

namespace App\Models;

use App\Support\Abstracts\CommentableModel;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends CommentableModel
{
    use HasFactory;
    use MergesParamsToRequest;

    const DEFAULT_ORDER_BY = 'year';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    protected $guarded = ['id'];
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function countryCodes()
    {
        return $this->belongsToMany(CountryCode::class)
            ->withPivot('comment');
    }

    public function marketingAuthorizationHoldersForCountryCode($countryCodeID)
    {
        return $this->belongsToMany(MarketingAuthorizationHolder::class, 'plan_country_code_marketing_authorization_holder')
            ->wherePivot('country_code_id', $countryCodeID)
            ->withPivot(
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Querying
    |--------------------------------------------------------------------------
    */

    public static function getAll()
    {
        return self::orderBy(self::DEFAULT_ORDER_BY, self::DEFAULT_ORDER_TYPE)
            ->withCount('comments')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Create and Update
    |--------------------------------------------------------------------------
    */

    public static function createFromRequest($request)
    {
        $instance = self::create($request->all());

        // HasMany relations
        $instance->storeComment($request->comment);
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());

        // HasMany relations
        $this->storeComment($request->comment);
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    // Implement the abstract method declared in the CommentableModel class
    public function getTitle(): string
    {
        return $this->year;
    }
}
