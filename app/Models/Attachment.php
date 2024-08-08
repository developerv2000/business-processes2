<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    // Ensure all relevant fields are mass assignable
    protected $guarded = ['id'];
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the parent attachable model (morphTo relationship).
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Boot the model and register event listeners.
     */
    protected static function booted()
    {
        static::creating(function ($instance) {
            $instance->created_at = now();
        });

        // Delete the file from storage when the attachment is deleted
        static::deleting(function ($instance) {
            $instance->deleteFileFromStorage();
        });
    }

    /**
     * Delete the file associated with the attachment from storage.
     *
     * @return void
     */
    public function deleteFileFromStorage()
    {
        // Construct the full file path relative to the public directory
        $filePath = public_path($this->file_path);

        // Ensure the file exists before trying to delete it
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Get the file size in megabytes.
     *
     * @return float
     */
    public function getFileSizeInMegabytesAttribute()
    {
        return round($this->file_size / (1024 * 1024), 2);
    }
}
