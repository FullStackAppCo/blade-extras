<?php

declare(strict_types=1);

use FullStackAppCo\RectorExtras\Rules\HelperFunctionToFacadeRector;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withSkip([
        ClosureToArrowFunctionRector::class,
    ])
    ->withRules([
        DeclareStrictTypesRector::class,
        HelperFunctionToFacadeRector::class,
    ])
    ->withImportNames()
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0);
