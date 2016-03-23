<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\HttpFoundation\Image\UploadedImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CoolwayFestivales\BackendBundle\Entity\Artist
 * @ORM\Table(name="artist")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\ArtistRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Artist {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string $id_spotify
     * @ORM\Column(name="id_spotify", type="string", length=50, nullable=true)
     */
    private $id_spotify;

    /**
     * @var string $website
     * @ORM\Column(name="website", type="string", length=250, nullable=true)
     */
    private $website;

    /**
     * @var string $twitter
     * @ORM\Column(name="twitter", type="string", length=250, nullable=true)
     */
    private $twitter;

    /**
     * @var string $facebook
     * @ORM\Column(name="facebook", type="string", length=250, nullable=true)
     */
    private $facebook;

    /**
     * @var string $instagram
     * @ORM\Column(name="instagram", type="string", length=250, nullable=true)
     */
    private $instagram;

    /**
     * @var string $path
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var string $cover
     * @ORM\Column(name="cover", type="string", length=255, nullable=true)
     */
    private $cover;

    private $temp;

    /**
     * @Assert\Image(maxSize ="1M", mimeTypes = {"image/jpg","image/png","image/gif","image/jpeg"})
     */
    protected $image;

    /**
     * @OneToMany(targetEntity="FeastStageArtist", mappedBy="artist", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    public $feast_stages_artist;

    public function __construct() {}

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Artist
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function __toString() {
        return $this->getName();
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Artist
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set id_spotify
     *
     * @param string $id_spotify
     * @return Artist
     */
    public function setIdSpotify($id_spotify) {
        $this->id_spotify = $id_spotify;

        return $this;
    }

    /**
     * Get id_spotify
     *
     * @return string
     */
    public function getIdSpotify() {
        return $this->id_spotify;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Artist
     */
    public function setWebsite($website) {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite() {
        return $this->website;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return Artist
     */
    public function setTwitter($twitter) {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter() {
        return $this->twitter;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return Artist
     */
    public function setFacebook($facebook) {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook() {
        return $this->facebook;
    }

    /**
     * Set instagram
     *
     * @param string $instagram
     * @return Artist
     */
    public function setInstagram($instagram) {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * Get instagram
     *
     * @return string
     */
    public function getInstagram() {
        return $this->instagram;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Artist
     */
    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set cover
     *
     * @param string $cover
     * @return Artist
     */
    public function setCover($cover) {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string
     */
    public function getCover() {
        return $this->cover;
    }

    /**
     * Set image.
     *
     * @param UploadedImage $image
     * @return User
     */
    public function setImage(UploadedFile $image = null) {
        $this->image = $image;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
        return $this;
    }

    /**
     * Get image
     *
     * @return UploadedImage
     */
    public function getImage() {
        return $this->image;
    }

    protected function getUploadDir() {
        return 'uploads/artist';
    }

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getAbsolutePath() {
        return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath() {
        return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (NULL !== $this->image) {
            $this->path = uniqid() . '.' . $this->getImage()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->getImage()) {
            return;
        }
        if (isset($this->temp)) {
            try {
                unlink($this->getUploadRootDir() . '/' . $this->temp);
            } catch (\Exception $e) {
                // nothing to do
            }
            $this->temp = null;
        }
        $this->getImage()->move($this->getUploadRootDir(), $this->path);
        $this->image = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        $path = $this->getAbsolutePath();
        try {
            if (file_exists($path) && !is_dir($path))
                unlink($path);
        } catch (\Exception $e) {
            // nothing to do
        }
    }

    /**
     * Add feast_stages_artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\FeastStageArtist $feastStagesArtist
     * @return Artist
     */
    public function addFeastStagesArtist(\CoolwayFestivales\BackendBundle\Entity\FeastStageArtist $feastStagesArtist) {
        $this->feast_stages_artist[] = $feastStagesArtist;

        return $this;
    }

    /**
     * Remove feast_stages_artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\FeastStageArtist $feastStagesArtist
     */
    public function removeFeastStagesArtist(\CoolwayFestivales\BackendBundle\Entity\FeastStageArtist $feastStagesArtist) {
        $this->feast_stages_artist->removeElement($feastStagesArtist);
    }

    /**
     * Get feast_stages_artist
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeastStagesArtist() {
        return $this->feast_stages_artist;
    }

} // end class