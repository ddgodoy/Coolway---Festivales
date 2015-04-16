<?php

namespace CoolwayFestivales\SafetyBundle\Domain;

use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\TreeWalkerAdapter;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\Exec\SingleSelectExecutor;

/**
 * Description of Aclwalker
 *
 * @author rali
 */
class Aclwalker extends SqlWalker {

    public function walkFromClause($fromClause) {
        $sql = parent::walkFromClause($fromClause);
        $tableAlias = $this->getSQLTableAlias($this->getQuery()->getHint('acl.entityRootTableName'), $this->getQuery()->getHint('acl.entityRootTableDqlAlias'));
        $extraQuery = $this->getQuery()->getHint('acl.extra.query');

//JOIN ({$extraQuery}) ta_ ON {$tableAlias}.id = ta_.id::integer /*para postgres*/
        $tempAclView = <<<tempAclView
        JOIN ({$extraQuery}) ta_ ON {$tableAlias}.id = ta_.id
tempAclView;

        return $sql . $tempAclView;
    }

}
