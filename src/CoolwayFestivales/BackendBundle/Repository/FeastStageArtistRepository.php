<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 *
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeastStageArtistRepository extends EntityRepository {

	public function getLineup()
	{
		$now = date('Y-m-d H:i:00');
		$q = $this->getEntityManager()->createQuery(
			"SELECT fsa.time, fsa.date, a.name as artist, a.id as artist_id, s.name as stage, s.id as stage_id FROM BackendBundle:FeastStageArtist fsa
			LEFT JOIN BackendBundle:FeastStage fs WITH fsa.feast_stage = fs.id
			LEFT JOIN BackendBundle:Feast f WITH fs.feast = f.id
			LEFT JOIN BackendBundle:Artist a WITH fsa.artist = a.id
			LEFT JOIN BackendBundle:Stage s WITH fs.stage = s.id
			WHERE f.date_from <= '$now' AND f.date_to >= '$now'
			ORDER BY fsa.date ASC, s.name ASC, fsa.time ASC"
		);

		return $q->getResult();
	}

	public function findNextArtist()
	{
		$time_from = date('H:i:00');
                $time_to = date('H:i:00',strtotime("+15 minutes"));
		$date = date('Y-m-d');
		//$date= '2015-04-29';
		//$time = '20:00:00';
		$q = $this->getEntityManager()->createQuery(
			"SELECT a.id as id, a.name as artist, s.name as stage FROM BackendBundle:FeastStageArtist fsa
			LEFT JOIN BackendBundle:Artist a WITH fsa.artist = a.id
			LEFT JOIN BackendBundle:FeastStage fs WITH fsa.feast_stage = fs.id
			LEFT JOIN BackendBundle:Stage s WITH fs.stage = s.id
			WHERE fsa.time = '$time_from' AND fsa.date = '$date'"
		);

		$q->setMaxResults(1);

		try {
			return $q->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return false;
		}
	}
}
