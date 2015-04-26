<?php

namespace CoolwayFestivales\SafetyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository implements ContainerAwareInterface
{
    private $container;


    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }    
    
     public function getList(){
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->from("SafetyBundle:User", "user")
            ->select("user");
                
        $aclHelper = $this->container->get('acl.helper'); //se llama al helper
        $query = $aclHelper->apply($qb, array("VIEW"));
        $result = $query->getResult();
        return $result;
        
    }

    public function findNotification() {
        $q = $this->getEntityManager()->createQuery(
            "SELECT u FROM SafetyBundle:User u
            WHERE u.notificationId != 'NULL'"
        );
        
        return $q->getResult();
    }
}