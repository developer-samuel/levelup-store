<?php

declare(strict_types=1);

namespace Tools\Rector\Rules;

use PhpParser\{
    Node,
    Node\Stmt\Class_
};

use Rector\{
    Privatization\NodeManipulator\VisibilityManipulator,
    Rector\AbstractRector
};

use Symplify\{
    RuleDocGenerator\ValueObject\CodeSample\CodeSample,
    RuleDocGenerator\ValueObject\RuleDefinition
};

final class FinalizeNonAbstractClassRector extends AbstractRector
{
    /**
     * @param VisibilityManipulator $visibilityManipulator
    */
    public function __construct(
        private readonly VisibilityManipulator $visibilityManipulator,
    ) {}

    /**
     * @return RuleDefinition
    */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add final to all non-abstract, non-entity classes.',
            [
                new CodeSample(
                    'class Foo extends Bar {}',
                    'final class Foo extends Bar {}',
                ),
            ],
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->isAbstract()) {
            return null;
        }

        if ($node->isFinal()) {
            return null;
        }

        if ($node->isAnonymous()) {
            return null;
        }

        if ($this->isDoctrineEntity($node)) {
            return null;
        }

        $this->visibilityManipulator->makeFinal($node);

        return $node;
    }

    private function isDoctrineEntity(Class_ $node): bool
    {
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $name = $attr->name->toString();
                if (str_contains($name, 'Entity') || str_contains($name, 'Document') || str_contains($name, 'MappedSuperclass')) {
                    return true;
                }
            }
        }

        return false;
    }
}
