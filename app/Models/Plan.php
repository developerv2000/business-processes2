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
            ->withPivot(self::getPivotColumnNames());
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

    public function storeCountryCodeFromRequest($request)
    {
        $countryCode = CountryCode::find($request->country_code_id);

        $this->countryCodes()->attach($countryCode, [
            'comment' => $request->comment,
        ]);

        return $countryCode;
    }

    public static function getPivotColumnNames(): array
    {
        return [
            'January_contract_plan',
            'February_contract_plan',
            'March_contract_plan',
            'April_contract_plan',
            'May_contract_plan',
            'June_contract_plan',
            'July_contract_plan',
            'August_contract_plan',
            'September_contract_plan',
            'October_contract_plan',
            'November_contract_plan',
            'December_contract_plan',

            'January_register_plan',
            'February_register_plan',
            'March_register_plan',
            'April_register_plan',
            'May_register_plan',
            'June_register_plan',
            'July_register_plan',
            'August_register_plan',
            'September_register_plan',
            'October_register_plan',
            'November_register_plan',
            'December_register_plan',

            'January_comment',
            'February_comment',
            'March_comment',
            'April_comment',
            'May_comment',
            'June_comment',
            'July_comment',
            'August_comment',
            'September_comment',
            'October_comment',
            'November_comment',
            'December_comment',
        ];
    }
}
