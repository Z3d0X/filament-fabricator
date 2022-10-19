<?php

namespace Z3d0X\FilamentFabricator\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string $slug
 * @property-read string $layout
 * @property-read array $blocks
 * @property-read int $parent_id
 * @property-read \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Support\Carbon $updated_at
 */
class Page extends Model
{
    use HasFactory;

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
        static::saved(fn () => Cache::forget('filament-fabricator::page-urls'));
        static::deleted(fn () => Cache::forget('filament-fabricator::page-urls'));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->select('id', 'slug', 'title', 'parent_id')->with('children:id,slug,title,parent_id');
    }
}
