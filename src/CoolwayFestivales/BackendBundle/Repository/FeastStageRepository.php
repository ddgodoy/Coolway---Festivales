<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use CoolwayFestivales\BackendBundle\Entity\FeastStage;
use Doctrine\ORM\EntityRepository;
use Proxies\__CG__\CoolwayFestivales\BackendBundle\Entity\Stage;

/**
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeastStageRepository extends EntityRepository
{
    public function findInFestival($feast_id)
    {
        $q = $this->getEntityManager()->createQuery(
            "SELECT fs FROM BackendBundle:FeastStage fs
			WHERE fs.feast = $feast_id
			ORDER BY fs.id ASC"
        );
        return $q->getResult();
    }
    //
    public function setFiltroByUser($authorization, $storage)
    {
        if ($authorization->isGranted('ROLE_SUPER_ADMIN'))
        {
            $filtro = 'fs.feast > 0';
        } else {
            $token  = $storage->getToken();
            $user   = $token->getUser();
            $filtro = 'fs.feast = '.$user->getFeast()->getId();
        }
        return $filtro;
    }
    //
    public function addStageOnTheFly($feast, $stage)
    {
        $em = $this->getEntityManager();
        $oF = $em->getRepository('BackendBundle:Feast')->find($feast);

        $oStage = new Stage();
        $oStage->setName($stage);

        $em->persist($oStage); $em->flush();

        $oRel = new FeastStage();
        $oRel->setFeast($oF);
        $oRel->setStage($oStage);

        $em->persist($oRel); $em->flush();
    }

} // end class