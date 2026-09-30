<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

/**
 * Macro for adding CHECK constraints to tables in a portable and expressive way.
*/
final class CheckConstraintMacro
{
    /**
     * Add a CHECK constraint to a given table.
     *
     * @param Table $table The table instance
     * @param string $name Constraint name
     * @param string $expression SQL condition expression (without the 'CHECK()' wrapper)
     *
     * @return void
    */
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

    /**
     * Add or append a human-readable CHECK description to the table comment.
     *
     * @param Table $table
     * @param string $expression
     *
     * @return void
    */
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
