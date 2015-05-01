<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 *
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserFeastDataRepository extends EntityRepository {

	public function findRanking($id) {
		$q = $this->getEntityManager()->createQuery(
			"SELECT u.name as user, u.notificationId as notificationId, u.id as user_id, SUM( fd.total ) as total  FROM BackendBundle:UserFeastData fd
			LEFT JOIN SafetyBundle:User u WITH fd.user = u.id
			WHERE fd.feast = $id 
			GROUP BY fd.user
			ORDER BY total DESC"
		);
		
		return $q->getResult();
	}

	public function findTimeline($feast_id,$user_id ) {
		$q = $this->getEntityManager()->createQuery(
			"SELECT  Date(fd.date) as fecha , SUM(fd.total) as total, SUM(fd.dance) as dance, SUM(fd.music) as music, fd.date  FROM BackendBundle:UserFeastData fd
			WHERE fd.feast = $feast_id  AND fd.user = $user_id
			GROUP BY fecha
			ORDER BY fd.date DESC"
		);
		
		return $q->getResult();
	}

	public function findTotalDay($feast_id,$date ) {
		
		$q = $this->getEntityManager()->createQuery(
			"SELECT Date(fd.date) as fecha, SUM(fd.total) as total FROM BackendBundle:UserFeastData fd
			WHERE fd.feast = $feast_id AND Date(fd.date) = '$date'
			GROUP BY fecha
			ORDER BY fd.date DESC"
		);
		$q->setMaxResults(1);
		
		try {
			return $q->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return false;
		}
	}

	public function findUsersForDay($feast_id,$date ) {
		$q = $this->getEntityManager()->createQuery(
			"SELECT SUM(fd.total) as total FROM BackendBundle:UserFeastData fd
			WHERE fd.feast = $feast_id  AND Date(fd.date) = '$date'
			GROUP BY fd.user"
		);
		
		return $q->getResult();
	}

	public function findUsersForFeast($feast_id) {
		$q = $this->getEntityManager()->createQuery(
			"SELECT fd.total FROM BackendBundle:UserFeastData fd
			WHERE fd.feast = $feast_id
			GROUP BY fd.user"
		);
		
		return $q->getResult();
	}
	

	public function findMyTotal($feast_id,$user_id ) {
		$q = $this->getEntityManager()->createQuery(
			"SELECT SUM(fd.total) as total, SUM(fd.dance) as dance, SUM(fd.music) as music, fd.date  FROM BackendBundle:UserFeastData fd
			WHERE fd.feast = $feast_id  AND fd.user = $user_id
			GROUP BY fd.user"
		);
		
		$q->setMaxResults(1);

		try {
			return $q->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return false;
		}
	}

	public function findLastData($user_id ) {
		$q = $this->getEntityManager()->createQuery(
			"SELECT fd.total as total, fd.dance as dance, fd.music as music, fd.date  FROM BackendBundle:UserFeastData fd
			WHERE fd.user = $user_id
			ORDER BY fd.date DESC"
		);
		$q->setMaxResults(1);
		
		try {
			return $q->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return false;
		}
	}

	public function findLastDataNotNull($feast,$user_id ) {
		$q = $this->getEntityManager()->createQuery(
			"SELECT fd.total as total, fd.dance as dance, fd.music as music, fd.date  FROM BackendBundle:UserFeastData fd
			WHERE fd.user = $user_id AND fd.feast = $feast AND fd.total > 0 
			ORDER BY fd.date DESC"
		);
		$q->setMaxResults(1);
		
		try {
			return $q->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return false;
		}
	}

	public function findTotal($feast_id ) {
		$q = $this->getEntityManager()->createQuery(
			"SELECT SUM(fd.total) as total FROM BackendBundle:UserFeastData fd
			WHERE fd.feast = $feast_id
			GROUP BY fd.feast"
		);
		$q->setMaxResults(1);
		
		try {
			return $q->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return false;
		}
	}

}
