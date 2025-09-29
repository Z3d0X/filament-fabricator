<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetProvider;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/routes',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withCache()
    ->withParallel()
    ->withRules([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        TypedPropertyFromStrictConstructorRector::class,
    ])
    ->withSkip([
        // Only add classes that give false positives here
        ClosureToArrowFunctionRector::class,
        DisallowedEmptyRuleFixerRector::class,
        RemoveAlwaysTrueIfConditionRector::class,
        RemoveUnreachableStatementRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,
        SimplifyUselessVariableRector::class,
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        earlyReturn: true,
    )
    ->withSetProviders(LaravelSetProvider::class)
    ->withComposerBased(laravel: true, phpunit: true)
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_110,
    ]);
