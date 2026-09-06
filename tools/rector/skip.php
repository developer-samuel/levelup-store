<?php

declare(strict_types=1);

use Rector\{
    CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector,
    CodingStyle\Rector\If_\NullableCompareToNullRector,
    CodingStyle\Rector\Stmt\NewlineAfterStatementRector,
    DeadCode\Rector\ClassMethod\RemoveParentDelegatingConstructorRector,
    DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector,
    DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector,
    DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector,
    DeadCode\Rector\For_\RemoveDeadIfForeachForRector,
    DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector,
    DeadCode\Rector\Switch_\RemoveDuplicatedCaseInSwitchRector,
    TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector
};

return [
    // DeadCode
    RemoveDeadIfForeachForRector::class,
    RemoveDuplicatedCaseInSwitchRector::class,
    RemoveNonExistingVarAnnotationRector::class,
    RemoveParentDelegatingConstructorRector::class,
    RemoveUnusedVariableAssignRector::class,
    RemoveUselessParamTagRector::class,
    RemoveUselessReturnTagRector::class,

    // CodingStyle
    NewlineAfterStatementRector::class,
    NewlineBetweenClassLikeStmtsRector::class,
    NullableCompareToNullRector::class,

    // TypeDeclaration
    AddMethodCallBasedStrictParamTypeRector::class,
    '#.*empty.*#i'
];
