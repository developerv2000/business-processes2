<?php

namespace App\Models;

use App\Support\Abstracts\UsageCountableModel;

class CountryCode extends UsageCountableModel
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    public function kvpps()
    {
        return $this->hasMany(Kvpp::class);
    }

    public static function getAll()
    {
        return self::orderBy('usage_count', 'desc')->orderBy('name')->get();
    }

    // Implement method declared in UsageCountableModel abstract class
    public function recalculateUsageCount(): void
    {
        $this->update([
            'usage_count' => $this->processes()->count() + $this->kvpps()->count(),
        ]);
    }
}
