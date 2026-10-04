<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class CheckConstraintMacro
{
    public static function add(Table $table, string $name, string $expression): void
    {
        /** @var array<mixed> $rawOptions */
        $rawOptions = $table->getOptions();
        $options = is_array($rawOptions) ? $rawOptions : [];

        if (!isset($options['check_constraints']) || !is_array($options['check_constraints'])) {
            $options['check_constraints'] = [];
        }

        $options['check_constraints'][$name] = $expression;
        $table->addOption('check_constraints', $options['check_constraints']);

        self::addComment($table, $expression);
    }

    private static function addComment(Table $table, string $expression): void
    {
        $comment = '';

        $existingComment = $table->hasOption('comment') ? $table->getOption('comment') : '';

        if (is_string($existingComment)) {
            $comment = $existingComment . ' ';
        }

        $comment .= sprintf('CHECK: %s', $expression);

        $table->addOption('comment', trim($comment));
    }
}
