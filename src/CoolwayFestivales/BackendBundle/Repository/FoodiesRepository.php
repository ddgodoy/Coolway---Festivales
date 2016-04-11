<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FoodiesRepository extends EntityRepository
{
    public function findInFestival($feast_id)
    {
        $q = $this->getEntityManager()->createQuery(
            "SELECT f FROM BackendBundle:Foodies f
			WHERE f.feast = $feast_id
			ORDER BY f.id DESC"
        );
        return $q->getResult();
    }
    //
    public function cleanSocialNetworksValues($entity)
    {
        $twitter   = $this->wipeOutSocialString($entity->getTwitter());
        $facebook  = $this->wipeOutSocialString($entity->getFacebook());
        $instagram = $this->wipeOutSocialString($entity->getInstagram());

        $entity->setTwitter  ($twitter);
        $entity->setFacebook ($facebook);
        $entity->setInstagram($instagram);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
    //
    public function wipeOutSocialString($social)
    {
        $clean = '';

        if (!empty($social))
        {
            $slash = substr($social, -1);

            if ($slash == '/') {
                $social = substr($social, 0, -1);
            }
            $chunks = explode('/', $social);
            $i_cant = count($chunks);
            $clean  = $chunks[$i_cant-1];
        }
        return $clean;
    }
    //
    public function setRequiredImages($entity)
    {
        $aFlags = array('foto' => false, 'portada' => false);

        if (empty($entity->getPath()))  { $aFlags['foto'] = true; }
        if (empty($entity->getCover())) { $aFlags['portada'] = true; }

        return $aFlags;
    }

} // end class