<?php

namespace Z3d0X\FilamentFabricator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as Contract;

class Page extends Model implements Contract
{
    protected $fillable = [
        'title',
        'slug',
        'blocks',
        'layout',
        'parent_id',
    ];

    protected $casts = [
        'blocks' => 'array',
        'parent_id' => 'integer',
    ];

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('filament-fabricator::page-urls'));
        static::deleted(fn() => Cache::forget('filament-fabricator::page-urls'));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->select('id', 'slug', 'title', 'parent_id')->with('children:id,slug,title,parent_id');
    }
}
