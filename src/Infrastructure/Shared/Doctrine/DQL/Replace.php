<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Doctrine\DQL;

use Doctrine\{
    ORM\Query\AST\Functions\FunctionNode,
    ORM\Query\AST\Node,
    ORM\Query\Parser,
    ORM\Query\SqlWalker,
    ORM\Query\TokenType
};

final class Replace extends FunctionNode
{
    private Node $str;
    private Node $search;
    private Node $replace;

    /**
     * @param Parser $parser
     *
     * @return void
    */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->str = $parser->StringPrimary();

        $parser->match(TokenType::T_COMMA);

        $this->search = $parser->StringPrimary();

        $parser->match(TokenType::T_COMMA);

        $this->replace = $parser->StringPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param SqlWalker $sqlWalker
     *
     * @return string
    */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'REPLACE(%s, %s, %s)',
            $this->str->dispatch($sqlWalker),
            $this->search->dispatch($sqlWalker),
            $this->replace->dispatch($sqlWalker),
        );
    }
}
