<?php

namespace App\Models;

use App\Support\Abstracts\CommentableModel;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Plan extends CommentableModel
{
    use HasFactory;
    use MergesParamsToRequest;

    const DEFAULT_ORDER_BY = 'year';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    protected $guarded = ['id'];
    public $timestamps = false;

    protected $with = [
        'countryCodes',
        'lastComment',
    ];

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
    | Events
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::deleting(function ($instance) {
            foreach ($instance->comments as $comment) {
                $comment->delete();
            }

            foreach ($instance->countryCodes as $countryCode) {
                $instance->detachCountryCodeByID($countryCode->id);
            }
        });
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

    public static function mergeDefaultParamsToRequest($request)
    {
        $request->mergeIfMissing([
            'year' => date('Y'),
        ]);
    }

    public static function getByYearFromRequest($request)
    {
        $year = $request->input('year');

        return self::where('year', $year)->first();
    }

    /**
     * Used in route plan.country.codes.destroy
     */
    public function detachCountryCodeByID($countryCodeID)
    {
        $countryCode = CountryCode::find($countryCodeID);
        $marketingAuthorizationHolders = $countryCode->marketingAuthorizationHoldersForPlan($this->id)->get();
        $marketingAuthorizationHolderIDs = $marketingAuthorizationHolders->pluck('id');

        // Detach marketing authorization holders first
        $this->detachMarketingAuthorizationHolders($countryCode, $marketingAuthorizationHolderIDs);

        // Then detach country code
        $this->countryCodes()->detach([$countryCodeID]);
    }

    /**
     * Used in route plan.marketing.authorization.holders.destroy
     * and plan.country.codes.destroy
     */
    public function detachMarketingAuthorizationHolders($countryCode, $marketingAuthorizationHolderIDs)
    {
        foreach ($marketingAuthorizationHolderIDs as $mahID) {
            DB::table('plan_country_code_marketing_authorization_holder')->where([
                'plan_id' => $this->id,
                'country_code_id' => $countryCode->id,
                'marketing_authorization_holder_id' => $mahID,
            ])->delete();
        }
    }

    public function loadMarketingAuthorizationHoldersOfCountries()
    {
        foreach ($this->countryCodes as $countryCode) {
            $countryCode->plan_marketing_authorization_holders = $countryCode->marketingAuthorizationHoldersForPlan($this->id)->get();
        }
    }

    public function makeAllCalculationsAndAddLinksFromRequest($request)
    {
        $this->loadMarketingAuthorizationHoldersOfCountries();

        foreach ($this->countryCodes as $countryCode) {
            // Calculate MAH all processes count
            foreach ($countryCode->plan_marketing_authorization_holders as $mah) {
                $mah->calculatePlanAllProcessesCountFromRequest($request);
                $mah->addPlanProcesseslinkFromRequest($request);
                $mah->calculatePlanAllPercentages($request);

                $mah->calculatePlanQuoterProcessesCounts();
                $mah->calculatePlanQuoterPercentages();

                $mah->calculatePlanYearProcessCounts();
                $mah->calculatePlanYearPercentages();
            }

            // // Calculate country code quoter & total processes count
            // $countryCode->calculateQuoterProcessesCountForPlan($request);
            // $countryCode->calculateTotalProcessesCountForPlan($request);
        }
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
