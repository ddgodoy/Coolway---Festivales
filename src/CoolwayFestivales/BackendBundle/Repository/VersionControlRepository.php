<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use CoolwayFestivales\BackendBundle\Entity\VersionControl;
use Doctrine\ORM\EntityRepository;

class VersionControlRepository extends EntityRepository
{
    public function updateVersionNumber($feast_id)
    {
        $updated = '';
        $now = new \DateTime('now');
        $em = $this->getEntityManager();
        $q = $em->createQuery("SELECT vc FROM BackendBundle:VersionControl vc WHERE vc.feast = $feast_id");
        $q->setMaxResults(1);

        $obj = $q->getOneOrNullResult();

        if ($obj)
        {
            $current  = $obj->getVersion();
            $toNumber = substr($current, 1);
            $arPartes = explode('.', $toNumber);

            if ($arPartes[1] == 9) {
                $arPartes[1] = 0;
                $arPartes[0] += 1;
            } else {
                $arPartes[1] += 1;
            }
            $updated = 'v'.$arPartes[0].'.'.$arPartes[1];
        } else {
            $feast = $em->getRepository('BackendBundle:Feast')->find($feast_id);

            if ($feast) {
                $obj = new VersionControl();
                $obj->setFeast($feast);
                $updated = 'v1.0';
            }
        }
        if (!empty($updated))
        {
            $obj->setVersion($updated);
            $obj->setCreated($now);

            $em->persist($obj);
            $em->flush();
        }
    }
    //
    public function updateForAllFestivals()
    {
        $festivals = $this->getEntityManager()->getRepository('BackendBundle:Feast')->findAll();

        foreach ($festivals as $item)
        {
            $this->updateVersionNumber($item->getId());
        }
    }

} // end class