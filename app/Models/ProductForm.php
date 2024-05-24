<?php

namespace App\Models;

use App\Support\Interfaces\ParentableInterface;
use App\Support\Interfaces\TemplatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductForm extends Model implements ParentableInterface, TemplatedModelInterface
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    public $with = [
        'parent'
    ];

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'form_id');
    }

    public function kvpps()
    {
        return $this->hasMany(Kvpp::class, 'form_id');
    }

    public function childs()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getParentNameAttribute()
    {
        return $this->parent ? $this->parent->name : $this->name;
    }

    public static function getAllPrioritizedAndMinifed()
    {
        return self::withOnly([])->get()->sortByDesc('usage_count');
    }

    // Implement the method declared in the ParentableInterface
    public function scopeOnlyParents()
    {
        return self::whereNull('parent_id')->orderBy('name')->get();
    }

    // Implement the method declared in the TemplatedModelInterface
    public function getUsageCountAttribute(): int
    {
        return $this->products()->count()
            + $this->kvpps()->count();
    }

    /**
     * Get the IDs of all related records in the family tree, including the current record.
     *
     * If the current record has a parent, it includes the IDs of all its children and itself.
     * If the current record has no parent, it includes only its own ID.
     *
     * @return \Illuminate\Support\Collection|array The IDs of all related records in the family tree.
     */
    public function getFamilyIDs()
    {
        // If the current record has a parent, use the parent; otherwise, use the current record itself
        $parent = $this->parent ?: $this;

        // Pluck child IDs and push parent ID
        $IDs = $parent->childs->pluck('id')->toArray();
        $IDs[] = $parent->id;

        return $IDs;
    }
}
