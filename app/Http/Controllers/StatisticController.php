<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Models\ProcessGeneralStatus;
use App\Models\ProcessStatusHistory;
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

        // Collect Monthes
        $monthes = self::collectMonthes();

        if ($request->month) {
            $monthes = $monthes->where('number', $request->month)->all();
        }

        // Get general statusses
        $generalStatusses = ProcessGeneralStatus::query()
            ->when(!$request->extensive, function ($statusses) {
                $statusses->where('stage', '<=', 5);
            })
            ->orderBy('stage', 'asc')
            ->get();

        // Add required attributes with null values, to avoid errors and duplications
        self::addRequiredAttributesForStatusses($generalStatusses, $monthes);

        // Add current processes count of each month for statusses. Table 1
        self::addStatusCurrentProcessesCount($request, $generalStatusses, $monthes);
        // Add transitional processes count of each month for statusses. Table 2
        self::addStatusTransitionalProcessesCount($request, $generalStatusses, $monthes);

        // Calculate total current process and total transition processes of each statusses (Table 1 and Table 2)
        self::calculateStatusTotalProcessesCount($generalStatusses);
        // Calculate total current process and total transition processes of each monthes (Table 1 and Table 2)
        self::calculateMonthTotalProcessesCount($generalStatusses, $monthes);

        // Calculate sum of all total current processes count of statusses
        $sumOfTotalCurrentProcessesCount = $generalStatusses->sum('total_current_processes_count');
        // Calculate sum of all total transitional processes count of statusses
        $sumOfTotalTransitionalProcessesCount = $generalStatusses->sum('total_transitional_processes_count');

        return view('statistics.index', compact('request', 'monthes', 'generalStatusses', 'sumOfTotalCurrentProcessesCount', 'sumOfTotalTransitionalProcessesCount'));
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
            'month' => null,
            'extensive' => false,
            'year' => date('Y'),
        ]);
    }

    /**
     * Collect monthes.
     *
     * @return \Illuminate\Support\Collection
     */
    private static function collectMonthes()
    {
        return collect([
            collect(['name' => 'January', 'number' => 1]),
            collect(['name' => 'February', 'number' => 2]),
            collect(['name' => 'March', 'number' => 3]),
            collect(['name' => 'April', 'number' => 4]),
            collect(['name' => 'May', 'number' => 5]),
            collect(['name' => 'June', 'number' => 6]),
            collect(['name' => 'July', 'number' => 7]),
            collect(['name' => 'August', 'number' => 8]),
            collect(['name' => 'September', 'number' => 9]),
            collect(['name' => 'October', 'number' => 10]),
            collect(['name' => 'November', 'number' => 11]),
            collect(['name' => 'December', 'number' => 12]),
        ]);
    }

    /**
     * Add required attributes for statuses.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatusses
     * @param  \Illuminate\Support\Collection  $monthes
     * @return void
     */
    private static function addRequiredAttributesForStatusses($generalStatusses, $monthes)
    {
        foreach ($generalStatusses as $status) {
            $array = array();

            foreach ($monthes as $month) {
                // Add monthes attributes
                $array[$month['number']] = [
                    'current_processes_count' => 0,
                    'transitional_processes_count' => 0,
                ];

                $status->monthes = $array;

                // Add total counts
                $status->total_current_processes_count = 0;
                $status->total_transitional_processes_count = 0;
            }
        }
    }

    /**
     * Add current processes count by monthes for each statuses.
     *
     * Iterates through general statuses and monthes to add the count of current processes for each month of status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatusses
     * @param  \Illuminate\Support\Collection  $monthes
     * @return void
     */
    private static function addStatusCurrentProcessesCount($request, $generalStatusses, $monthes)
    {
        foreach ($generalStatusses as $status) {
            foreach ($monthes as $month) {
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

                // Get current processes count of the month fot the status
                $monthProcessesCount = $query->count();

                // Set current processes count of the month fot the status
                $statusMonth = $status->monthes;
                $statusMonth[$month['number']]['current_processes_count'] = $monthProcessesCount;
                $status->monthes = $statusMonth;
            }
        }
    }

    /**
     * Add transitional processes count by monthes for each statuses.
     *
     * Iterates through general statuses and monthes to add the count of transitional processes for each month of status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatusses
     * @param  \Illuminate\Support\Collection  $monthes
     * @return void
     */
    private static function addStatusTransitionalProcessesCount($request, $generalStatusses, $monthes)
    {
        foreach ($generalStatusses as $status) {
            foreach ($monthes as $month) {
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

                // Get transitional processes count of the month fot the status
                $monthProcessesCount = $query->count();

                // Set transitional processes count of the month fot the status
                $statusMonth = $status->monthes;
                $statusMonth[$month['number']]['transitional_processes_count'] = $monthProcessesCount;
                $status->monthes = $statusMonth;
            }
        }
    }

    /**
     * Calculate total current processes count
     * and total transitional processes count for each status.
     *
     * Iterates through general statuses and calculates the total current and transitional processes count
     * based on the counts for each month.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatusses
     * @return void
     */
    private static function calculateStatusTotalProcessesCount($generalStatusses)
    {
        foreach ($generalStatusses as $status) {
            $totalCurrentProcesses = 0;
            $totalTransitionalProcesses = 0;

            foreach ($status->monthes as $month) {
                $totalCurrentProcesses += $month['current_processes_count'];
                $totalTransitionalProcesses += $month['transitional_processes_count'];
            }

            $status->total_current_processes_count = $totalCurrentProcesses;
            $status->total_transitional_processes_count = $totalTransitionalProcesses;
        }
    }

    /**
     * Calculate total current processes count
     * and total transitional processes count for each month.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $generalStatusses
     * @param  \Illuminate\Support\Collection  $monthes
     * @return void
     */
    private static function calculateMonthTotalProcessesCount($generalStatusses, $monthes)
    {
        foreach ($monthes as $month) {
            $totalCurrentProcesses = 0;
            $totalTransitionalProcesses = 0;

            foreach ($generalStatusses as $status) {
                $totalCurrentProcesses += $status->monthes[$month['number']]['current_processes_count'];
                $totalTransitionalProcesses += $status->monthes[$month['number']]['transitional_processes_count'];
            }

            $month['total_current_processes_count'] = $totalCurrentProcesses;
            $month['total_transitional_processes_count'] = $totalTransitionalProcesses;
        }
    }
}
