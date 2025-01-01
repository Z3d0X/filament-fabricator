<?php

namespace Z3d0X\FilamentFabricator\Models\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property-read int|string $id
 * @property-read string $title
 * @property-read string $slug
 * @property-read string $layout
 * @property-read array $blocks
 * @property-read int|string|null $parent_id
 * @property-read self|null $parent
 * @property-read Collection<array-key,self> $children
 * @property-read Collection<array-key,self> $allChildren
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
interface Page extends HasPageUrls
{
    public function parent(): BelongsTo;

    public function children(): HasMany;

    public function allChildren(): HasMany;

    /** @return \Illuminate\Database\Eloquent\Builder */
    public static function query();
}
