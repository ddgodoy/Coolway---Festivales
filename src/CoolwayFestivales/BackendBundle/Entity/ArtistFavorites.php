<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * CoolwayFestivales\BackendBundle\Entity\ArtistFavorites
 * @ORM\Table(name="artist_favorites")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\ArtistFavoritesRepository")
 */
class ArtistFavorites {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Artist", cascade={"all"}, fetch="EAGER")
     */
    private $artist;

    /**
     * @ManyToOne(targetEntity="\CoolwayFestivales\SafetyBundle\Entity\User", cascade={"all"}, fetch="EAGER")
     */
    private $user;

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
     * Set artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Artist $artist
     * @return ArtistFavorites
     */
    public function setArtist(\CoolwayFestivales\BackendBundle\Entity\Artist $artist = null) {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist
     *
     * @return \CoolwayFestivales\BackendBundle\Entity\Artist
     */
    public function getArtist() {
        return $this->artist;
    }


    /**
     * Set user
     *
     * @param \CoolwayFestivales\SafetyBundle\Entity\User $user
     * @return ArtistFavorites
     */
    public function setUser(\CoolwayFestivales\SafetyBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CoolwayFestivales\SafetyBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
