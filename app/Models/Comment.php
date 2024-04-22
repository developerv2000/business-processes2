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

    public function commentable()
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::creating(function ($item) {
            $item->created_at = $item->created_at ?: now();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
