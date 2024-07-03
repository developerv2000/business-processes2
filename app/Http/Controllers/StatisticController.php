<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Models\ProcessGeneralStatus;
use App\Models\ProcessStatusHistory;
use App\Support\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * There are extensive and minified versions of statistics.
 * Minified version is always used for non-admins.
 * Extensive / minified version switcher is available for admins,
 * and extensive version is used as default admins.
 *
 * Table 1 - Current processes count for each month of general status
 * Table 2 - Maximum processes count for each month of general status
 *
 * On minified version only first 5 stages of general statuses are shown,
 * and specific query is used for stage 5 (Kk) for the 'Current processes count table' (Table 1).
 * On extensive version all stages of general statuses are shown,
 *
 * Specific query in Table 1 for stage 5 (Kk) gets:
 * count of all processes which have current status stage == 5 (Kk) for the requested month and year
 * + count of all processes which have current status stage > 5 (6КД - 10Отмена)
 * and had contract (status stage == 5 (Kk)) requested year.
 *
 * Some tricky methods are used to calculate processes count for both of tables (Table 1 / Table 2):
 * On minified version general statuses 'name' is compared with processes general statuses 'name_for_analysts',
 * because 'name_for_analysts' of stages > 5 are the same as 'name' of stage 5 (Kk).
 * On extensive version general statuses 'id' is compared with processes general statuses 'id'
 */
class StatisticController extends Controller
{
    public function index(Request $request)
    {
        self::mergeDefaultParamsToRequest($request);
        $months = self::getFilteredMonths($request);

        $generalStatuses = self::getFilteredGeneralStatuses($request);
        self::addRequiredAttributesForGeneralStatuses($generalStatuses, $months);

        // Calculate Table 1 - Current processes count
        self::addCurrentProcessesCountForStatusMonths($request, $generalStatuses, $months);
        // Add current processes link for each month of statuses. Table 1
        self::addCurrentProcessesLinkForStatusMonths($request, $generalStatuses);

        // Calculate Table 2 - Maximum processes count
        self::addMaximumProcessesCountForStatusMonths($request, $generalStatuses, $months);
        // Add maximum processes link for each month of statuses. Table 2
        self::addMaximumProcessesLinkForStatusMonths($request, $generalStatuses);

        // Calculate general statuses 'year_current_processes_count' and 'year_maximum_processes_count' (Table 1 and Table 2)
        self::calculateStatusesYearProcessesCount($generalStatuses);
        // Calculate month 'all_current_process_count' and 'all_maximum_process_count' (Table 1 and Table 2)
        self::calculateMonthAllProcessesCount($generalStatuses, $months);

        // Calculate sum of all 'year_current_processes_count' of general statuses (Table 1)
        $yearTotalCurrentProcessesCount = $generalStatuses->sum('year_current_processes_count');
        // Calculate sum of all 'year_maximum_processes_count' of general statuses (Table 2)
        $yearTotalMaximumProcessesCount = $generalStatuses->sum('year_maximum_processes_count');

        return view('statistics.index', compact('request', 'months', 'generalStatuses', 'yearTotalCurrentProcessesCount', 'yearTotalMaximumProcessesCount'));
    }

    /**
     * Merge default parameters to the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private static function mergeDefaultParamsToRequest($request)
    {
        $request->mergeIfMissing([
            'year' => date('Y'),
        ]);

        $user = $request->user();

        // Make extensive version default for admins
        if ($user->isAdmin()) {
            $request->mergeIfMissing([
                'extensive_version' => true,
            ]);
        } else {
            // Restrict non-admin users to only see their own process statistics
            $request->merge([
                'analyst_user_id' => $user->id,
            ]);
        }
    }

    /**
     * Get filtered months based on the request parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private static function getFilteredMonths($request)
    {
        // Define the array of months
        $months = Helper::collectCalendarMonths();

        // If specific months are requested, filter the months array
        if ($request->months) {
            $months = $months->whereIn('number', $request->months)
                ->sortBy('number')
                ->values();
        }

        return $months->all();
    }

    /**
     * Get filtered general statuses based on the request parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private static function getFilteredGeneralStatuses($request)
    {
        // Query to retrieve general statuses
        $query = ProcessGeneralStatus::query();

        // Apply filtering based on request parameters
        $query->when(!$request->extensive_version, function ($statuses) {
            $statuses->where('stage', '<=', 5);
        });

        // Order the statuses by stage in ascending order
        $query->orderBy('stage', 'asc');

        // Retrieve and return the filtered general statuses
        return $query->get();
    }

    /**
     * Add required attributes with null values, to avoid errors and duplications
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @param  \Illuminate\Support\Collection  $months
     * @return void
     */
    private static function addRequiredAttributesForGeneralStatuses($generalStatuses, $months)
    {
        foreach ($generalStatuses as $status) {
            $monthsArray = array();

            foreach ($months as $month) {
                $monthsArray[$month['number']] = [
                    'number' => $month['number'],
                    'current_processes_count' => 0,
                    'maximum_processes_count' => 0,
                    'current_processes_link' => '#',
                    'maximum_processes_link' => '#',
                ];

                $status->months = $monthsArray;

                // Add year counts
                $status->year_current_processes_count = 0;
                $status->year_maximum_processes_count = 0;
            }
        }
    }

    /*
    |-------------------------------------------------------
    | Helper functions for Table 1 - Current processes count
    |-------------------------------------------------------
    */

    /**
     * Add current processes count by months for each general statuses.
     *
     * Iterates through general statuses and months to add the count of current processes for each month of status.
     * Read controller documentation for more details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @param  \Illuminate\Support\Collection  $months
     * @return void
     */
    private static function addCurrentProcessesCountForStatusMonths($request, $generalStatuses, $months)
    {
        foreach ($generalStatuses as $status) {
            foreach ($months as $month) {
                $query = Process::whereMonth('status_update_date', $month['number'])
                    ->whereYear('status_update_date', $request->year);

                // Extensive version
                if ($request->extensive_version) {
                    $query = $query->whereHas('status.generalStatus', function ($statusesQuery) use ($status) {
                        $statusesQuery->where('id', $status->id);
                    });
                    // Minified version
                } else {
                    // Specific query for Stage 5 (Kk) of minified version
                    if ($status->stage == 5) {
                        $query = Process::filterRecordsContractedOnRequestedMonthAndYear($query, $request->year, $month['number']);
                    } else {
                        // Query for stages < 5 (1ВП - 4СЦ) of minified version
                        $query = $query->whereHas('status.generalStatus', function ($statusesQuery) use ($status) {
                            $statusesQuery->where('id', $status->id);
                        });
                    }
                }

                // Additional filtering
                $query = self::filterCurrentProcessesQuery($request, $query);

                // Get current processes count of the month for the status
                $monthProcessesCount = $query->count();

                // Set current processes count of the month for the status
                $statusMonths = $status->months;
                $statusMonths[$month['number']]['current_processes_count'] = $monthProcessesCount;

                $status->months = $statusMonths;
            }
        }
    }

    /**
     * Filter the given processes query based on request parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function filterCurrentProcessesQuery($request, $query)
    {
        $whereEqualAttributes = [
            'country_code_id',
        ];

        $whereRelationEqualStatements = [
            [
                'name' => 'manufacturer',
                'attribute' => 'bdm_user_id',
            ],
            [
                'name' => 'manufacturer',
                'attribute' => 'analyst_user_id',
            ],
        ];

        $query = Helper::filterQueryWhereEqualStatements($request, $query, $whereEqualAttributes);
        $query = Helper::filterWhereRelationEqualStatements($request, $query, $whereRelationEqualStatements);
        $query = Process::filterSpecificManufacturerCountry($request, $query);

        return $query;
    }

    /**
     * Add current processes link for status months based on request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $generalStatuses
     * @return void
     */
    private static function addCurrentProcessesLinkForStatusMonths($request, $generalStatuses)
    {
        // Filter parameters for the query
        $queryParams = self::getFilterQueryParameters($request);

        foreach ($generalStatuses as $status) {
            foreach ($status->months as $month) {
                // Prepare a copy of the query with 'status_update_date' range
                $queryParamsCopy = $queryParams;
                $queryParamsCopy['status_update_date'] = self::generateStatusUpdateRangeForLinks($request->year, $month['number']);

                // Extensive version
                if ($request->extensive_version) {
                    $queryParamsCopy['general_status_id'] = $status->id;
                } else {
                    // Minified version
                    if ($status->stage == 5) {
                        // Special links are used for stage 5 (Kk) on minified version
                        $queryParamsCopy['contracted_on_requested_month_and_year'] = true;
                        $queryParamsCopy['contracted_month'] = $month['number'];
                        $queryParamsCopy['contracted_year'] = $request->year;
                    } else {
                        // Stages 1 - 4 (1ВП - 4СЦ)
                        $queryParamsCopy['name_for_analysts'] = $status->name_for_analysts;
                    }
                }

                // Generate the current processes link based on the query
                $currentProcessesLink = route('processes.index', $queryParamsCopy);

                // Update the status object with the current processes link
                $statusMonths = $status->months;
                $statusMonths[$month['number']]['current_processes_link'] = $currentProcessesLink;
                $status->months = $statusMonths;
            }
        }
    }

    /*
    |-------------------------------------------------------
    | Helper functions for Table 2 - Maximum processes count
    |-------------------------------------------------------
    */

    /**
     * Add maximum processes count by months for each statuses.
     *
     * Iterates through general statuses and months to add the count of maximum processes for each month of status.
     * Read controller documentation for more details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @param  \Illuminate\Support\Collection  $months
     * @return void
     */
    private static function addMaximumProcessesCountForStatusMonths($request, $generalStatuses, $months)
    {
        foreach ($generalStatuses as $status) {
            foreach ($months as $month) {
                $query = ProcessStatusHistory::whereMonth('start_date', $month['number'])
                    ->whereYear('start_date', $request->year);

                // Extensive version
                if ($request->extensive_version) {
                    $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                        $q->where('id', $status->id);
                    });
                    // Minified version
                } else {
                    $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                        $q->where('name_for_analysts', $status->name);
                    });
                }

                // Additional filtering
                $query = self::filterMaximumProcessesQuery($request, $query);

                // Unique by process_id
                $query = $query->distinct('process_id');

                // Get maximum processes count of the month fot the status
                $monthProcessesCount = $query->count();

                // Set maximum processes count of the month fot the status
                $statusMonths = $status->months;
                $statusMonths[$month['number']]['maximum_processes_count'] = $monthProcessesCount;
                $status->months = $statusMonths;
            }
        }
    }

    /**
     * Filter the given process status history query based on request parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $historyQuery
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function filterMaximumProcessesQuery($request, $query)
    {
        $whereRelationEqualStatements = [
            [
                'name' => 'process.manufacturer',
                'attribute' => 'analyst_user_id',
            ],

            [
                'name' => 'process.manufacturer',
                'attribute' => 'bdm_user_id',
            ],

            [
                'name' => 'process',
                'attribute' => 'country_code_id',
            ],
        ];

        $query = Helper::filterWhereRelationEqualStatements($request, $query, $whereRelationEqualStatements);

        $query = $query->whereHas('process', function ($processesQuery) use ($request) {
            return Process::filterSpecificManufacturerCountry($request, $processesQuery);
        });

        return $query;
    }

    /**
     * Add current processes link for status months based on request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $generalStatuses
     * @return void
     */
    private static function addMaximumProcessesLinkForStatusMonths($request, $generalStatuses)
    {
        // Filter parameters for the query
        $queryParams = self::getFilterQueryParameters($request);

        foreach ($generalStatuses as $status) {
            foreach ($status->months as $month) {
                // Prepare a copy of the query with 'status_update_date' range
                $queryParamsCopy = $queryParams;
                $queryParamsCopy['has_status_history'] = true;
                $queryParamsCopy['has_status_history_on_year'] = $request->year;
                $queryParamsCopy['has_status_history_on_month'] = $month['number'];

                // Extensive version
                if ($request->extensive_version) {
                    $queryParamsCopy['has_status_history_based_on_id'] = true;
                    $queryParamsCopy['has_status_history_general_status_id'] = $status->id;
                } else {
                    $queryParamsCopy['has_status_history_based_on_name_for_analysts'] = true;
                    $queryParamsCopy['has_status_history_general_status_name_for_analysts'] = $status->name;
                }

                // Generate the current processes link based on the query
                $maximumProcessesLink = route('processes.index', $queryParamsCopy);

                // Update the status object with the maximum processes link
                $statusMonths = $status->months;
                $statusMonths[$month['number']]['maximum_processes_link'] = $maximumProcessesLink;
                $status->months = $statusMonths;
            }
        }
    }

    /*
    |---------------------------------------
    | Helper functions for Table 1 - Table 2
    |---------------------------------------
    */

    /**
     * Get filter query parameters from the request.
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    private static function getFilterQueryParameters($request)
    {
        return [
            'analyst_user_id' => $request->analyst_user_id,
            'bdm_user_id' => $request->bdm_user_id,
            'country_code_id' => $request->country_code_id,
            'specific_manufacturer_country' => $request->specific_manufacturer_country,
        ];
    }

    /**
     * Generate status update date range for links based on the given month.
     *
     * @param int $year
     * @param int $month
     * @return string
     */
    private static function generateStatusUpdateRangeForLinks($year, $month)
    {
        $monthStart = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-01');
        $nextMonthStart = $monthStart->copy()->addMonth()->startOfMonth();

        return $monthStart->format('d/m/Y') . ' - ' . $nextMonthStart->format('d/m/Y');
    }

    /**
     * Iterates through general statuses and calculates the total current and maximum processes count
     * based on the counts for each month.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @return void
     */
    private static function calculateStatusesYearProcessesCount($generalStatuses)
    {
        foreach ($generalStatuses as $status) {
            $yearCurrentProcessesCount = 0;
            $yearMaximumProcessesCount = 0;

            foreach ($status->months as $month) {
                $yearCurrentProcessesCount += $month['current_processes_count'];
                $yearMaximumProcessesCount += $month['maximum_processes_count'];
            }

            $status->year_current_processes_count = $yearCurrentProcessesCount;
            $status->year_maximum_processes_count = $yearMaximumProcessesCount;
        }
    }

    /**
     * Calculate total current processes count
     * and total maximum processes count for each month.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @param  \Illuminate\Support\Collection  $months
     * @return void
     */
    private static function calculateMonthAllProcessesCount($generalStatuses, $months)
    {
        foreach ($months as $month) {
            $totalCurrentProcesses = 0;
            $totalMaximumProcesses = 0;

            foreach ($generalStatuses as $status) {
                $totalCurrentProcesses += $status->months[$month['number']]['current_processes_count'];
                $totalMaximumProcesses += $status->months[$month['number']]['maximum_processes_count'];
            }

            $month['all_current_process_count'] = $totalCurrentProcesses;
            $month['all_maximum_process_count'] = $totalMaximumProcesses;
        }
    }
}
