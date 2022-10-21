<?php

namespace Z3d0X\FilamentFabricator\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string $slug
 * @property-read string $layout
 * @property-read array $blocks
 * @property-read int $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Z3d0X\FilamentFabricator\Models\Contracts\Page[] $children
 * @property-read \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Support\Carbon $updated_at
 */
interface Page
{
    public function parent(): BelongsTo;

    public function children(): HasMany;

    public function allChildren(): HasMany;

    /** @return \Illuminate\Database\Eloquent\Builder */
    public static function query();
}
