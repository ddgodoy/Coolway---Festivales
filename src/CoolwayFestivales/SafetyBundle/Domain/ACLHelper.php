<?php

namespace CoolwayFestivales\SafetyBundle\Domain;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ACL
 * 
 * @author rali
 */
class ACLHelper
{

    private $em;
    private $securityContext;
    
    function __construct($doctrine, $securityContext)
    {
        $this->em = $doctrine->getManager();
        $this->securityContext = $securityContext;
        $this->aclConnection = $doctrine->getConnection('default');
    }

    protected function cloneQuery(Query $query)
    {
        $aclAppliedQuery = clone $query;
        $params = $query->getParameters();
        foreach ($params as $key => $param) {
            $aclAppliedQuery->setParameter($key, $param);
        }

        return $aclAppliedQuery;
    }

    /**
     * This will clone the original query and 
     * @param QueryBuilder $queryBuilder
     * @param array $permissions
     * @return type 
     */
    public function apply(QueryBuilder $queryBuilder,array $permissions = array("VIEW"))
    {

        $whereQueryParts = $queryBuilder->getDQLPart('where');
        if (empty($whereQueryParts)) {
            $fromQueryParts = $queryBuilder->getDQLPart('from');
            $firstFromQueryAlias = $fromQueryParts[0]->getAlias();
            $queryBuilder->where($firstFromQueryAlias . '.id > 0'); // this will help in cases where no where query is specified, where query is required to walk in where clause
        }

        $query = $this->cloneQuery($queryBuilder->getQuery());

        $builder = new MaskBuilder();
        foreach ($permissions as $permission) {
            $mask = constant(get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $query->setHint('acl.mask', $builder->get());

        //Change this to the place where you saved your Aclwalker
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER,'CoolwayFestivales\SafetyBundle\Domain\Aclwalker');
        $entities = $queryBuilder->getRootEntities();
        $query->setHint('acl.root.entities', $entities);

        $query->setHint('acl.extra.query',
                $this->getPermittedIdsACLSQLForUser($query, $queryBuilder));

        $class = $this->em->getClassMetadata($entities[0]);
        $entityRootTableName = $class->getQuotedTableName($this->em->getConnection()->getDatabasePlatform());
        $entityRootAlias = $queryBuilder->getRootAlias();
        $query->setHint('acl.entityRootTableName', $entityRootTableName);
        $query->setHint('acl.entityRootTableDqlAlias', $entityRootAlias);
        
        return $query;
    }

    /**
     * @param Query $query
     * @param QueryBuilder $queryBuilder
     * @return String Sql 
     */
    private function getPermittedIdsACLSQLForUser(Query $query,QueryBuilder $queryBuilder)
    {
        $database = $this->aclConnection->getDatabase();
        $mask = $query->getHint('acl.mask');
        $rootEntities = $query->getHint('acl.root.entities');

        foreach ($rootEntities as $rootEntity) {
            $class = $this->em->getClassMetadata($rootEntity);
//            $rE[] = "'" .$class->getName(). "'";//para postgres
            $rE[] = "'" .str_replace('\\', '\\\\',$class->getName()). "'";
            // For now ACL will be checked for first root entity, it will not check for all other entities in join etc..,
            break;
        }
        $rootEntities = implode(',', $rE);

        $token = $this->securityContext->getToken(); // for now lets imagine we will have token i.e user is logged in
        $user = $token->getUser();
        $INString = "''";
 
        $uR=array();
        if (is_object($user)) {
            $userRoles = $user->getRoles();
            foreach ($userRoles as $role) {
               // The reason we ignore this is because by default FOSUserBundle adds ROLE_USER for every user
                if ($role !== 'ROLE_USER'){
                    $uR[] = "'" . $role . "'";
                } 
            }if(count($uR)>0)//por si no hay roles
            $INString = implode('OR s.identifier =', (array) $uR);
            
//            $INString .= " OR s.identifier = '" . str_replace('\\', '\\', para postgres
            $INString .= " OR s.identifier = '" . str_replace('\\', '\\\\',
                            get_class($user)) . '-' . $user->getUserName() . "'";
        }
        
        $selectQuery = <<<SELECTQUERY
          SELECT DISTINCT o.object_identifier as id FROM acl_object_identities as o 
          INNER JOIN acl_classes c ON c.id = o.class_id
          LEFT JOIN acl_entries e ON (
                e.class_id = o.class_id AND (e.object_identity_id = o.id OR {$this->aclConnection->getDatabasePlatform()->getIsNullExpression('e.object_identity_id')})
            )
         LEFT JOIN acl_security_identities s ON (
                s.id = e.security_identity_id
            )
          WHERE  c.class_type = {$rootEntities}
          AND s.identifier =  {$INString}
          AND e.mask >= {$mask}
        
SELECTQUERY;
//        echo "<pre>";
//        print_r($selectQuery);
//        die;

        return $selectQuery;
    }

}

?>
