<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingAuthorizationHolder extends Model implements TemplatedModelInterface
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

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
        return self::orderBy('id')->get();
    }

    // Implement the method declared in the TemplatedModelInterface
    public function getUsageCountAttribute(): int
    {
        return $this->processes()->count()
            + $this->kvpps()->count();
    }
}
