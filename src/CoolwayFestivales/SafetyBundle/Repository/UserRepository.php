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

    public function cleanRanking($feast) {
        $q = $this->getEntityManager()->createQuery(
            "UPDATE SafetyBundle:User u SET u.feast = $feast , u.total = 0"
        );
        return $q->getResult();
    }

    public function findRanking() {
        $q = $this->getEntityManager()->createQuery(
            "SELECT u FROM SafetyBundle:User u
            ORDER BY u.total DESC"
        );
        $q->setMaxResults(50);
        
        return $q->getResult();
    }

    public function findRankingDash() {
        $q = $this->getEntityManager()->createQuery(
            "SELECT u FROM SafetyBundle:User u
            ORDER BY u.total DESC WHERE id > 1011"
        );
        
        return $q->getResult();
    }

    public function getPosition($user_id) {
        $q = $this->getEntityManager()->createQuery(
            "SELECT ( SELECT COUNT(x.id) FROM SafetyBundle:User x WHERE x.total >= u.total ORDER BY u.total DESC ) as position FROM SafetyBundle:User u
            WHERE u.id = $user_id"
        );
        
        return $q->getSingleResult();
    }

    public function getTotalFeast() {
        $q = $this->getEntityManager()->createQuery(
            "SELECT SUM(u.total) as total FROM SafetyBundle:User u
            GROUP BY u.feast"
        );
        $q->setMaxResults(1);

        try {
            return $q->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return 0;
        }
    }

    public function getTotalUsers() {
        $q = $this->getEntityManager()->createQuery(
            "SELECT COUNT(u.id) as total FROM SafetyBundle:User u"
        );
        $q->setMaxResults(1);

        try {
            return $q->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return 0;
        }
    }

    public function findNotification() {
        $q = $this->getEntityManager()->createQuery(
            "SELECT u FROM SafetyBundle:User u
            WHERE u.notificationId != 'NULL'"
        );
        
        return $q->getResult();
    }
}