<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Models\ProcessGeneralStatus;
use App\Models\ProcessStatusHistory;
use App\Support\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    /**
     * =============== Important ===============
     * All general status stages are used, while extensive statistics requested.
     * Else only first 5 general status stages are used,
     * and (stages > 5) are also included in the stage 5, while minified statistics requested
     *
     * Some tricky methods are used for querying:
     * General statuses are compared by id, while extensive statistics requested.
     * Else general statuses are compared by name_for_analysts, while minified statistics requested
     * =============== Important ===============
     */
    public function index(Request $request)
    {
        self::mergeDefaultParamsToRequest($request);
        $months = self::getFilteredMonths($request);
        $generalStatuses = self::getFilteredGeneralStatuses($request);

        // Add required attributes with null values, to avoid errors and duplications
        self::addRequiredAttributesForStatuses($generalStatuses, $months);

        // Add current processes count for each month of statuses. Table 1
        self::addCurrentProcessesCountForStatusMonths($request, $generalStatuses, $months);
        // Add permanent processes count for each month of statuses. Table 2
        self::addPermanentProcessesCountForStatusMonths($request, $generalStatuses, $months);

        // Add current processes link for each month of statuses. Table 1
        self::addCurrentProcessesLinkForStatusMonths($request, $generalStatuses);
        // Add permanent processes link for each month of statuses. Table 2
        // self::addPermanentProcessesLinkForStatusMonths($request, $generalStatuses);

        // Calculate total current process and total transition processes of each statuses (Table 1 and Table 2)
        self::calculateTotalProcessesCountForStatuses($generalStatuses);
        // Calculate total current process and total transition processes of each months (Table 1 and Table 2)
        self::calculateMonthTotalProcessesCountForStatuses($generalStatuses, $months);

        // Calculate sum of all total current processes count of statuses
        $sumOfTotalCurrentProcessesCount = $generalStatuses->sum('total_current_processes_count');
        // Calculate sum of all total permanent processes count of statuses
        $sumOfTotalPermanentProcessesCount = $generalStatuses->sum('total_permanent_processes_count');

        return view('statistics.index', compact('request', 'months', 'generalStatuses', 'sumOfTotalCurrentProcessesCount', 'sumOfTotalPermanentProcessesCount'));
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
            // 'months' => null,
            // 'extensive' => false,
            'year' => date('Y'),
        ]);

        // Restrict simple users to only see their own process statistics
        if (!$request->user()->isAdminOrModerator()) {
            $request->mergeIfMissing([
                'analyst_user_id' => $request->user()->id,
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
        $query->when(!$request->extensive, function ($statuses) {
            $statuses->where('stage', '<=', 5);
        });

        // Order the statuses by stage in ascending order
        $query->orderBy('stage', 'asc');

        // Retrieve and return the filtered general statuses
        return $query->get();
    }

    /**
     * Add required attributes for statuses.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @param  \Illuminate\Support\Collection  $months
     * @return void
     */
    private static function addRequiredAttributesForStatuses($generalStatuses, $months)
    {
        foreach ($generalStatuses as $status) {
            $array = array();

            foreach ($months as $month) {
                // Add months attributes
                $array[$month['number']] = [
                    'number' => $month['number'],
                    'current_processes_count' => 0,
                    'permanent_processes_count' => 0,
                    'current_processes_link' => '#',
                    'permanent_processes_link' => '#',
                ];

                $status->months = $array;

                // Add total counts
                $status->total_current_processes_count = 0;
                $status->total_permanent_processes_count = 0;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper functions for Table 1. Current general statuses
    |--------------------------------------------------------------------------
    */

    /**
     * Add current processes count by months for each statuses.
     *
     * Iterates through general statuses and months to add the count of current processes for each month of status.
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

                if ($request->extensive) {
                    $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                        $q->where('id', $status->id);
                    });
                } else {
                    $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                        $q->where('name_for_analysts', $status->name_for_analysts);
                    });
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
     * @param  \Illuminate\Database\Eloquent\Builder  $processesQuery
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function filterCurrentProcessesQuery($request, $processesQuery)
    {
        // Extract request parameters
        $analystID = $request->analyst_user_id;
        $bdmID = $request->bdm_user_id;
        $countryCodeID = $request->country_code_id;

        // Apply filters based on request parameters
        $processesQuery->when($analystID, function ($query) use ($analystID) {
            $query->whereHas('manufacturer', function ($manufacturerQuery) use ($analystID) {
                $manufacturerQuery->where('analyst_user_id', $analystID);
            });
        })
            ->when($bdmID, function ($query) use ($bdmID) {
                $query->whereHas('manufacturer', function ($manufacturerQuery) use ($bdmID) {
                    $manufacturerQuery->where('bdm_user_id', $bdmID);
                });
            })
            ->when($countryCodeID, function ($query) use ($countryCodeID) {
                $query->where('country_code_id', $countryCodeID);
            });

        return $processesQuery;
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
        $extensive = $request->extensive;
        $year = $request->year;

        // Filter parameters for the query
        $query = [
            'analyst_user_id' => $request->analyst_user_id,
            'bdm_user_id' => $request->bdm_user_id,
            'country_code_id' => $request->country_code_id,
        ];

        foreach ($generalStatuses as $status) {
            foreach ($status->months as $month) {
                $monthStart = Carbon::createFromFormat('Y-m-d', $year . '-' . $month['number'] . '-01');
                $nextMonthStart = $monthStart->copy()->addMonth()->startOfMonth();

                // Prepare a copy of the query with status update date range
                $queryCopy = $query;
                $queryCopy['status_update_date'] = $monthStart->format('d/m/Y') . ' - ' . $nextMonthStart->format('d/m/Y');

                // Depending on extensive flag, set additional query parameters
                if ($extensive) {
                    $queryCopy['general_status_id'] = $status->id;
                } else {
                    $queryCopy['name_for_analysts'] = $status->name_for_analysts;
                }

                // Generate the current processes link based on the query
                $currentProcessesLink = route('processes.index', $queryCopy);

                // Update the status object with the current processes link
                $statusMonths = $status->months;
                $statusMonths[$month['number']]['current_processes_link'] = $currentProcessesLink;
                $status->months = $statusMonths;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper functions for Table 2. Permanent general statuses
    |--------------------------------------------------------------------------
    */

    /**
     * Add permanent processes count by months for each statuses.
     *
     * Iterates through general statuses and months to add the count of permanent processes for each month of status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @param  \Illuminate\Support\Collection  $months
     * @return void
     */
    private static function addPermanentProcessesCountForStatusMonths($request, $generalStatuses, $months)
    {
        foreach ($generalStatuses as $status) {
            foreach ($months as $month) {
                $query = ProcessStatusHistory::whereMonth('start_date', $month['number'])
                    ->whereYear('start_date', $request->year);

                if ($request->extensive) {
                    $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                        $q->where('id', $status->id);
                    });
                } else {
                    $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                        $q->where('name_for_analysts', $status->name_for_analysts);
                    });
                }

                // Additional filtering
                $query = self::filterPermanentProcessesQuery($request, $query);

                // Get permanent processes count of the month fot the status
                $monthProcessesCount = $query->count();

                // Set permanent processes count of the month fot the status
                $statusMonths = $status->months;
                $statusMonths[$month['number']]['permanent_processes_count'] = $monthProcessesCount;
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
    private static function filterPermanentProcessesQuery($request, $historyQuery)
    {
        // Extract request parameters
        $analystID = $request->analyst_user_id;
        $bdmID = $request->bdm_user_id;
        $countryCodeID = $request->country_code_id;

        // Apply filters based on request parameters
        $historyQuery->when($analystID, function ($query) use ($analystID) {
            $query->whereHas('process.manufacturer', function ($manufacturerQuery) use ($analystID) {
                $manufacturerQuery->where('analyst_user_id', $analystID);
            });
        })
            ->when($bdmID, function ($query) use ($bdmID) {
                $query->whereHas('process.manufacturer', function ($manufacturerQuery) use ($bdmID) {
                    $manufacturerQuery->where('bdm_user_id', $bdmID);
                });
            })
            ->when($countryCodeID, function ($query) use ($countryCodeID) {
                $query->whereHas('process', function ($processQuery) use ($countryCodeID) {
                    $processQuery->where('country_code_id', $countryCodeID);
                });
            });

        return $historyQuery;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper functions for calculating total processes count. Table 1-2
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate total current processes count
     * and total permanent processes count for each month.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @param  \Illuminate\Support\Collection  $months
     * @return void
     */
    private static function calculateMonthTotalProcessesCountForStatuses($generalStatuses, $months)
    {
        foreach ($months as $month) {
            $totalCurrentProcesses = 0;
            $totalPermanentProcesses = 0;

            foreach ($generalStatuses as $status) {
                $totalCurrentProcesses += $status->months[$month['number']]['current_processes_count'];
                $totalPermanentProcesses += $status->months[$month['number']]['permanent_processes_count'];
            }

            $month['total_current_processes_count'] = $totalCurrentProcesses;
            $month['total_permanent_processes_count'] = $totalPermanentProcesses;
        }
    }

    /**
     * Calculate total current processes count
     * and total permanent processes count for each status.
     *
     * Iterates through general statuses and calculates the total current and permanent processes count
     * based on the counts for each month.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @return void
     */
    private static function calculateTotalProcessesCountForStatuses($generalStatuses)
    {
        foreach ($generalStatuses as $status) {
            $totalCurrentProcesses = 0;
            $totalPermanentProcesses = 0;

            foreach ($status->months as $month) {
                $totalCurrentProcesses += $month['current_processes_count'];
                $totalPermanentProcesses += $month['permanent_processes_count'];
            }

            $status->total_current_processes_count = $totalCurrentProcesses;
            $status->total_permanent_processes_count = $totalPermanentProcesses;
        }
    }
}
