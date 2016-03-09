<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use CoolwayFestivales\BackendBundle\Entity\Googlemap;
use Doctrine\ORM\EntityRepository;

class GooglemapRepository extends EntityRepository
{
    public function wipeOutCoordenadas($feast_id)
    {
        $q = $this->getEntityManager()->createQuery(
            "DELETE FROM BackendBundle:Googlemap g WHERE g.feast = $feast_id"
        );
        try {
            $q->execute();
        }
        catch (\Doctrine\ORM\NoResultException $e) {}
    }
    //
    public function addCoordinatesValues($feast_id, $name, $detail, $latitude, $longitude, $icono)
    {
        $em = $this->getEntityManager();
        $fs = $em->getRepository('BackendBundle:Feast')->find($feast_id);

        $oNew = new Googlemap();
        $oNew->setFeast    ($fs);
        $oNew->setName     ($name);
        $oNew->setDetail   ($detail);
        $oNew->setLatitude ($latitude);
        $oNew->setLongitude($longitude);
        $oNew->setPath     ($icono);

        $em->persist($oNew);
        $em->flush();
    }
    //
    public function getCoordinatesValues($feast_id)
    {
        $s = "SELECT g.name AS g_name, g.detail AS g_detail, g.latitude AS g_latitude, g.longitude AS g_longitude, g.path AS g_icono FROM BackendBundle:Googlemap g WHERE g.feast = $feast_id ORDER BY g.id";
        $q = $this->getEntityManager()->createQuery($s);

        try {
            return $q->getResult();
        }
        catch (\Doctrine\ORM\NoResultException $e)
        {
            return NULL;
        }
    }

} // end class