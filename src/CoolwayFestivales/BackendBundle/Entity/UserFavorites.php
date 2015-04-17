<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * CoolwayFestivales\BackendBundle\Entity\UserFavorites
 * @ORM\Table(name="user_favorites")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\UserFavoritesRepository")
 */
class UserFavorites {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\CoolwayFestivales\SafetyBundle\Entity\User", inversedBy="user_global", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\CoolwayFestivales\SafetyBundle\Entity\User", inversedBy="user_favorite", fetch="LAZY")
     * @ORM\JoinColumn(name="user_favorite_id", referencedColumnName="id")
     */
    protected $user_favorite;

    public function __construct() {

    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \CoolwayFestivales\SafetyBundle\Entity\User $user
     * @return UserFavorites
     */
    public function setUser(\CoolwayFestivales\SafetyBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CoolwayFestivales\SafetyBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Add user_favorites
     *
     * @param \CoolwayFestivales\SafetyBundle\Entity\User $userFavorites
     * @return UserFavorites
     */
    public function addUserFavorite(\CoolwayFestivales\SafetyBundle\Entity\User $userFavorites) {
        $this->user_favorites[] = $userFavorites;

        return $this;
    }

    /**
     * Remove user_favorites
     *
     * @param \CoolwayFestivales\SafetyBundle\Entity\User $userFavorites
     */
    public function removeUserFavorite(\CoolwayFestivales\SafetyBundle\Entity\User $userFavorites) {
        $this->user_favorites->removeElement($userFavorites);
    }

    /**
     * Get user_favorites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserFavorites() {
        return $this->user_favorites;
    }

    /**
     * Set user_favorite
     *
     * @param \CoolwayFestivales\SafetyBundle\Entity\User $userFavorite
     * @return UserFavorites
     */
    public function setUserFavorite(\CoolwayFestivales\SafetyBundle\Entity\User $userFavorite = null) {
        $this->user_favorite = $userFavorite;

        return $this;
    }

    /**
     * Get user_favorite
     *
     * @return \CoolwayFestivales\SafetyBundle\Entity\User
     */
    public function getUserFavorite() {
        return $this->user_favorite;
    }

}
