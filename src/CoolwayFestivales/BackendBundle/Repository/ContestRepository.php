<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use CoolwayFestivales\BackendBundle\Entity\Contest;
use CoolwayFestivales\SafetyBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;

class ContestRepository extends EntityRepository
{
    public function recordUpload($file, $feast)
    {
        $em = $this->getEntityManager();
        $now = new \DateTime('now');

        $obj = new Contest();
        $obj->setName    ($file->name);
        $obj->setFeast   ($feast);
        $obj->setLoadDate($now);

        $em->persist($obj);
        $em->flush();
    }
    //
    public function findInFestival($feast_id)
    {
        $q = $this->getEntityManager()->createQuery(
            "SELECT c FROM BackendBundle:Contest c
			WHERE c.feast = $feast_id
			ORDER BY c.id DESC"
        );
        return $q->getResult();
    }
    //
    public function clearAllAndSetNew($contest)
    {
        $e = $this->getEntityManager();
        $q = $e->createQuery("UPDATE BackendBundle:Contest c SET c.winner = NULL");
        $q->execute();

        if (!$contest->getWinner())
        {
            $contest->setWinner(true);
            $e->flush();
        }
    }

} // end class