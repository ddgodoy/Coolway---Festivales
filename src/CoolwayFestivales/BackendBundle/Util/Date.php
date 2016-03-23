<?php 
namespace CoolwayFestivales\BackendBundle\Util;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

class Date extends FunctionNode
{
    private $arg;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'DATE('.$this->arg->dispatch($sqlWalker).')';
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->arg = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}