<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArtistFavoritesRepository extends EntityRepository {

	public function findUserByArtist($artist)
	{
		$q = $this->getEntityManager()->createQuery
		(
			"SELECT u.notificationId as notificationId, u.os as os FROM BackendBundle:ArtistFavorites af
			LEFT JOIN SafetyBundle:User u WITH af.user = u.id
			WHERE af.artist = $artist"
		);
		return $q->getResult();
	}

} // end class