<?php

namespace Z3d0X\FilamentFabricator;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class Helpers
{
    /**
     * Group an array of associative arrays by a given key
     *
     * @param  array[]  $arr
     * @return array[]
     */
    public static function arrayRefsGroupBy(array &$arr, string $key): array
    {
        $ret = [];

        foreach ($arr as &$item) {
            $ret[$item[$key]][] = &$item;
        }

        return $ret;
    }

    /**
     * Helper function to make it easier to preload related models
     * via references in a block's data. Usually used inside `PageBlock::preloadRelatedData`
     *
     * @template TModel of Model
     *
     * @param array{
     *     type: string,
     *     data: array<string, mixed>,
     * }[] $blocks
     * @param  class-string<TModel>  $modelClass
     * @param  null|(\Closure(Builder): Builder)  $editQuery
     */
    public static function preloadRelatedModels(
        array &$blocks,
        string $property,
        string $modelClass,
        ?string $subProperty = null,
        ?Closure $editQuery = null,
        string $primaryKeyColumn = 'id',
    ): void {
        $editQuery ??= fn (Builder $builder) => $builder;
        $targetsSubProperty = $subProperty !== null;

        $ids = collect($blocks)
            ->lazy()
            ->map(function ($block) use ($targetsSubProperty, $property, $subProperty) {
                $collection = collect($block['data'][$property]);

                if ($targetsSubProperty) {
                    $collection = $collection->pluck($subProperty);
                }

                return $collection->all();
            })
            ->flatten()
            ->filter()
            ->unique()
            ->toArray();

        $query = $modelClass::query()
            ->whereIn($primaryKeyColumn, $ids);

        $query = $editQuery($query);

        /**
         * @var TModel[] $models
         */
        $models = $query->get();

        $models = collect($models)->groupBy($primaryKeyColumn);

        foreach ($blocks as &$block) {
            if ($targetsSubProperty) {
                foreach ($block['data'][$property] as &$item) {
                    $rawData = $item[$subProperty];
                    $item[$subProperty] = is_array($rawData)
                        ? array_map(fn ($key) => data_get($models, (string) $key)->first(), $rawData)
                        : data_get($models, (string) $rawData)->first();
                }
            } else {
                $rawData = $block['data'][$property];
                $block['data'][$property] = is_array($rawData)
                    ? array_map(fn ($key) => data_get($models, (string) $key)->first(), $rawData)
                    : data_get($models, (string) $rawData)->first();
            }
        }
    }
}
