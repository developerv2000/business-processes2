<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the parent commentable model.
     *
     * This defines a polymorphic relationship where a comment can belong to any model.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($instance) {
            $instance->created_at = $instance->created_at ?: now();
            $instance->updateManufacturerUpdatedAtColumn();
        });

        static::updating(function ($instance) {
            if ($instance->isDirty('created_at')) {
                $instance->updateManufacturerUpdatedAtColumn();
            }
        });
    }

    /**
     * Update the related manufacturer when the comment is being created/updated.
     *
     * This method checks if the commentable type is Manufacturer and updates its updated_at timestamp.
     */
    public function updateManufacturerUpdatedAtColumn()
    {
        if ($this->commentable_type == Manufacturer::class) {
            $manufacturer = $this->commentable;
            $manufacturer->update(['updated_at' => now()]);
        }
    }
}
