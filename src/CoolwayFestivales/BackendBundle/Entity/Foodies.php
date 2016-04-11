<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\HttpFoundation\Image\UploadedImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CoolwayFestivales\BackendBundle\Entity\Foodies
 * @ORM\Table(name="foodies")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\FoodiesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Foodies
{
    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Feast", fetch="EAGER")
     */
    private $feast;

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
     * Set feast
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Feast $feast
     * @return Foodies
     */
    public function setFeast(\CoolwayFestivales\BackendBundle\Entity\Feast $feast = null) {
        $this->feast = $feast;

        return $this;
    }

    /**
     * Get feast
     *
     * @return \CoolwayFestivales\BackendBundle\Entity\Feast
     */
    public function getFeast() {
        return $this->feast;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Foodies
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
     * @return Foodies
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
     * Set website
     *
     * @param string $website
     * @return Foodies
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
     * @return Foodies
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
     * @return Foodies
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
     * @return Foodies
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
     * @return Foodies
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
     * @return Foodies
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
     * @return Foodies
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
        return 'uploads/foodies';
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

} // end class