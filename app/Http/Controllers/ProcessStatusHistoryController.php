<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessStatusHistoryUpdateRequest;
use App\Models\Process;
use App\Models\ProcessStatus;
use App\Models\ProcessStatusHistory;
use Illuminate\Http\Request;

class ProcessStatusHistoryController extends Controller
{
    public function index(Request $request, Process $process)
    {
        $process->load(['product', 'manufacturer']);
        $records = $process->statusHistory()->orderBy('start_date')->get();

        return view('process-status-history.index', compact('process', 'records'));
    }

    public function edit(Process $process, ProcessStatusHistory $instance)
    {
        $statuses = ProcessStatus::getAll();

        return view('process-status-history.edit', compact('process', 'instance', 'statuses'));
    }

    public function update(ProcessStatusHistoryUpdateRequest $request, Process $process, ProcessStatusHistory $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    public function destroy(Request $request, Process $process)
    {
        $instance = ProcessStatusHistory::find($request->input('id'));
        $instance->destroyFromRequest($process);

        return to_route('process-status-history.index', [$process->id]);
    }
}
