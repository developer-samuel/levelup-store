<?php

declare(strict_types=1);

use Rector\{
    CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector,
    CodingStyle\Rector\Stmt\NewlineAfterStatementRector,
    DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector,
    DeadCode\Rector\ClassMethod\RemoveDuplicatedReturnSelfDocblockRector,
    DeadCode\Rector\ClassMethod\RemoveMixedDocblockOverruledByNativeTypeRector,
    DeadCode\Rector\ClassMethod\RemoveParentDelegatingConstructorRector,
    DeadCode\Rector\ClassMethod\RemoveReturnTagIncompatibleWithNativeTypeRector,
    DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector,
    DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector,
    DeadCode\Rector\ClassMethod\RemoveUselessUnionReturnDocblockRector,
    DeadCode\Rector\For_\RemoveDeadIfForeachForRector,
    DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector,
    DeadCode\Rector\Switch_\RemoveDuplicatedCaseInSwitchRector
};

return [
    // DeadCode
    RemoveDeadIfForeachForRector::class,
    RemoveDuplicatedCaseInSwitchRector::class,
    RemoveDuplicatedReturnSelfDocblockRector::class,
    RemoveMixedDocblockOverruledByNativeTypeRector::class,
    RemoveNonExistingVarAnnotationRector::class,
    RemoveParentDelegatingConstructorRector::class,
    RemoveReturnTagIncompatibleWithNativeTypeRector::class,
    RemoveUnusedVariableAssignRector::class,
    RemoveUselessParamTagRector::class,
    RemoveUselessReturnTagRector::class,
    RemoveUselessUnionReturnDocblockRector::class,

    // CodingStyle
    NewlineAfterStatementRector::class,
    NewlineBetweenClassLikeStmtsRector::class,

    '#.*empty.*#i'
];
