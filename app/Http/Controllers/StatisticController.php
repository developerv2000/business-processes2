<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Models\ProcessGeneralStatus;
use App\Models\ProcessStatusHistory;
use App\Support\Helper;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    /**
     * All general status stages are used,
     * while extensive statistics requested
     *
     * Else only first 5 stages are used,
     * and (stages > stage 5) are also included in the stage 5,
     * while minified statistics requested
     */
    public function index(Request $request)
    {
        self::mergeDefaultParamsToRequest($request);
        $months = self::getFilteredMonths($request);
        $generalStatuses = self::getFilteredGeneralStatuses($request);

        // Add required attributes with null values, to avoid errors and duplications
        self::addRequiredAttributesForStatuses($generalStatuses, $months);

        // Add current processes count of each month for statuses. Table 1
        self::addStatusCurrentProcessesCount($request, $generalStatuses, $months);
        // Add transitional processes count of each month for statuses. Table 2
        self::addStatusTransitionalProcessesCount($request, $generalStatuses, $months);

        // Calculate total current process and total transition processes of each statuses (Table 1 and Table 2)
        self::calculateStatusTotalProcessesCount($generalStatuses);
        // Calculate total current process and total transition processes of each months (Table 1 and Table 2)
        self::calculateMonthTotalProcessesCount($generalStatuses, $months);

        // Calculate sum of all total current processes count of statuses
        $sumOfTotalCurrentProcessesCount = $generalStatuses->sum('total_current_processes_count');
        // Calculate sum of all total transitional processes count of statuses
        $sumOfTotalTransitionalProcessesCount = $generalStatuses->sum('total_transitional_processes_count');

        return view('statistics.index', compact('request', 'months', 'generalStatuses', 'sumOfTotalCurrentProcessesCount', 'sumOfTotalTransitionalProcessesCount'));
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
                    'current_processes_count' => 0,
                    'transitional_processes_count' => 0,
                ];

                $status->months = $array;

                // Add total counts
                $status->total_current_processes_count = 0;
                $status->total_transitional_processes_count = 0;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper functions for Table 1. Status current processes count
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
    private static function addStatusCurrentProcessesCount($request, $generalStatuses, $months)
    {
        foreach ($generalStatuses as $status) {
            foreach ($months as $month) {
                $query = Process::whereMonth('status_update_date', $month['number'])
                    ->whereYear('status_update_date', $request->year);

                // Include all matching stages while extensive statistics requested
                if ($request->extensive) {
                    $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                        $q->where('stage', $status->stage);
                    });
                } else {
                    // Else Include all matching stages for (stages <=4) while minified statistics requested
                    if ($status->stage <= 4) {
                        $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                            $q->where('stage', $status->stage);
                        });
                        // And include all (stages >= 5) in the (STAGE == 5) while inified statistics requested
                    } else if ($status->stage == 5) {
                        $query = $query->whereHas('status.generalStatus', function ($q) {
                            $q->where('stage', '>=', 5);
                        });
                    }
                }

                // Additional filtering
                $query = self::filterCurrentProcessesQuery($request, $query);

                // Get current processes count of the month fot the status
                $monthProcessesCount = $query->count();

                // Set current processes count of the month fot the status
                $statusMonth = $status->months;
                $statusMonth[$month['number']]['current_processes_count'] = $monthProcessesCount;
                $status->months = $statusMonth;
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

    /*
    |--------------------------------------------------------------------------
    | Helper functions for Table 2. Status transitional processes count
    |--------------------------------------------------------------------------
    */

    /**
     * Add transitional processes count by months for each statuses.
     *
     * Iterates through general statuses and months to add the count of transitional processes for each month of status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @param  \Illuminate\Support\Collection  $months
     * @return void
     */
    private static function addStatusTransitionalProcessesCount($request, $generalStatuses, $months)
    {
        foreach ($generalStatuses as $status) {
            foreach ($months as $month) {
                $query = ProcessStatusHistory::whereMonth('start_date', $month['number'])
                    ->whereYear('start_date', $request->year);

                // Include all matching stages while extensive statistics requested
                if ($request->extensive) {
                    $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                        $q->where('stage', $status->stage);
                    });
                } else {
                    // Else Include all matching stages for (stages <=4) while minified statistics requested
                    if ($status->stage <= 4) {
                        $query = $query->whereHas('status.generalStatus', function ($q) use ($status) {
                            $q->where('stage', $status->stage);
                        });
                        // And include all (stages >= 5) in the (STAGE == 5) while inified statistics requested
                    } else if ($status->stage == 5) {
                        $query = $query->whereHas('status.generalStatus', function ($q) {
                            $q->where('stage', '>=', 5);
                        });
                    }
                }

                // Additional filtering
                $query = self::filterTransitionalProcessesQuery($request, $query);

                // Get transitional processes count of the month fot the status
                $monthProcessesCount = $query->count();

                // Set transitional processes count of the month fot the status
                $statusMonth = $status->months;
                $statusMonth[$month['number']]['transitional_processes_count'] = $monthProcessesCount;
                $status->months = $statusMonth;
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
    private static function filterTransitionalProcessesQuery($request, $historyQuery)
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
     * and total transitional processes count for each month.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @param  \Illuminate\Support\Collection  $months
     * @return void
     */
    private static function calculateMonthTotalProcessesCount($generalStatuses, $months)
    {
        foreach ($months as $month) {
            $totalCurrentProcesses = 0;
            $totalTransitionalProcesses = 0;

            foreach ($generalStatuses as $status) {
                $totalCurrentProcesses += $status->months[$month['number']]['current_processes_count'];
                $totalTransitionalProcesses += $status->months[$month['number']]['transitional_processes_count'];
            }

            $month['total_current_processes_count'] = $totalCurrentProcesses;
            $month['total_transitional_processes_count'] = $totalTransitionalProcesses;
        }
    }

    /**
     * Calculate total current processes count
     * and total transitional processes count for each status.
     *
     * Iterates through general statuses and calculates the total current and transitional processes count
     * based on the counts for each month.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatuses
     * @return void
     */
    private static function calculateStatusTotalProcessesCount($generalStatuses)
    {
        foreach ($generalStatuses as $status) {
            $totalCurrentProcesses = 0;
            $totalTransitionalProcesses = 0;

            foreach ($status->months as $month) {
                $totalCurrentProcesses += $month['current_processes_count'];
                $totalTransitionalProcesses += $month['transitional_processes_count'];
            }

            $status->total_current_processes_count = $totalCurrentProcesses;
            $status->total_transitional_processes_count = $totalTransitionalProcesses;
        }
    }
}
