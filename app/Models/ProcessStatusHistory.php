<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ProcessStatusHistory extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];
    public $with = ['status'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function status()
    {
        return $this->belongsTo(ProcessStatus::class);
    }

    /**
     * Close status history by updating the end date and calculating the duration.
     *
     * Called when process status is being updated
     *
     * @return void
     */
    public function close()
    {
        $this->update([
            'end_date' => now(),
            'duration_days' => $this->start_date->diffInDays(now()),
        ]);
    }

    /**
     * Determine if this status history is the active history of the associated process.
     *
     * @return bool True if this is the active status history of the process, false otherwise.
     */
    public function isActiveStatusHistory()
    {
        // Retrieve the process associated with the current process_id
        $process = Process::find($this->process_id);

        // Return false if the process is not found
        if (!$process) {
            return false;
        }

        // Check if this status_id matches the process's status_id and end_date is null
        return $process->status_id == $this->status_id && is_null($this->end_date);
    }

    /**
     * Update the model's attributes from the given request.
     *
     * @param \Illuminate\Http\Request $request The request object containing input data.
     * @return void
     */
    public function updateFromRequest($request)
    {
        // Update start_date from the request input
        $this->start_date = $request->input('start_date');

        // status_id and end_date cannot be updated for active status history
        if (!$this->isActiveStatusHistory()) {
            $this->status_id = $request->input('status_id');
            $this->end_date = $request->input('end_date');
            $this->duration_days = (int) $this->start_date->diffInDays($this->end_date);
            // Update status_update_date of related process
        } else {
            $process = $this->process;
            $process->status_update_date = $request->input('start_date');
            $process->timestamps = false;
            $process->saveQuietly();
        }

        $this->save();
    }

    /**
     * Delete the model if it is not the active status history of the process.
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException if the status history is active and cannot be deleted.
     */
    public function destroyFromRequest()
    {
        // Active status history cannot be deleted
        if ($this->isActiveStatusHistory()) {
            throw ValidationException::withMessages([
                'process_status_history_deletion' => trans('validation.custom.process_status_history.is_active_history'),
            ]);
        }

        // Delete the status history record
        $this->delete();
    }
}
