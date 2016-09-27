<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeastRepository extends EntityRepository
{
    public function findCurrent($onlyCurrent = false)
    {
        $now = date('Y-m-d H:i:00');
        $q = $this->getEntityManager()->createQuery(
            "SELECT f FROM BackendBundle:Feast f WHERE f.date_from <= '$now' AND f.date_to >= '$now'"
        );
        $q->setMaxResults(1);
        try {
            return $q->getSingleResult();
        }
        catch (\Doctrine\ORM\NoResultException $e)
        {
            if ($onlyCurrent)
                return false;

            $q = $this->getEntityManager()->createQuery(
                "SELECT f FROM BackendBundle:Feast f ORDER BY f.id ASC"
            );
            $q->setMaxResults(1);

            return $q->getOneOrNullResult();
        }
    }
    //
    public function listOfFeast($filtro)
    {
        $q = $this->getEntityManager()->createQuery(
            "SELECT f.id AS f_id, f.name AS f_name, f.latitude AS f_latitud, f.longitude AS f_longitud, f.path AS f_path FROM BackendBundle:Feast f WHERE $filtro ORDER BY f.name"
        );
        try {
            return $q->getResult();
        }
        catch (\Doctrine\ORM\NoResultException $e)
        {
            return NULL;
        }
    }
    //
    public function updateMapFeastValues($feast_id, $lat, $long, $image)
    {
        $em = $this->getEntityManager();

        $feast = $em->getRepository('BackendBundle:Feast')->find($feast_id);
        if($feast)
        {
            $feast->setImage($image);
            $feast->setLatitude($lat);
            $feast->setLongitude($long);

            $em->persist($feast);
            $em->flush();
        }

    }

} // end class