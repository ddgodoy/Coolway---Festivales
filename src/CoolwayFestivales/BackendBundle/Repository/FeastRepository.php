<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 *
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeastRepository extends EntityRepository {

    public function findCurrent() {
        $now = date('Y-m-d 00:00:00');
        $q = $this->getEntityManager()->createQuery(
                "SELECT f FROM BackendBundle:Feast f
			WHERE f.date_from <= '$now'  AND f.date_to >= '$now'"
        );

        $q->setMaxResults(1);
        try {
            return $q->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return false;
        }
    }

}
