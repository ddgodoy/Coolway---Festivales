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

} // end class