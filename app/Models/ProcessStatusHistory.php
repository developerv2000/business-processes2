<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
