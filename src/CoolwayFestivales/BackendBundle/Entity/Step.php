<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CoolwayFestivales\BackendBundle\Entity\Step
 * @ORM\Table(name="step")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\StepRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Step {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Feast", cascade={"all"}, fetch="EAGER")
     */
    private $feast;

    /**
     * @var string $steps
     * @ORM\Column(name="steps", type="integer")
     */
    private $steps;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * @var text
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

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
     * Set feast
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Feast $feast
     * @return Step
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
     * Set stage
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Stage $stage
     * @return Step
     */
    public function setStage(\CoolwayFestivales\BackendBundle\Entity\Stage $stage = null) {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return \CoolwayFestivales\BackendBundle\Entity\Stage
     */
    public function getStage() {
        return $this->stage;
    }

    /**
     * Add feast_stages_artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\StepArtist $feastStagesArtist
     * @return Step
     */
    public function addStepsArtist(\CoolwayFestivales\BackendBundle\Entity\StepArtist $feastStagesArtist) {
        $this->feast_stages_artist[] = $feastStagesArtist;

        return $this;
    }

    /**
     * Remove feast_stages_artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\StepArtist $feastStagesArtist
     */
    public function removeStepsArtist(\CoolwayFestivales\BackendBundle\Entity\StepArtist $feastStagesArtist) {
        $this->feast_stages_artist->removeElement($feastStagesArtist);
    }

    /**
     * Get feast_stages_artist
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStepsArtist() {
        return $this->feast_stages_artist;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return User
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
     * Set image.
     *
     * @param UploadedFile $image
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
     * @return UploadedFile
     */
    public function getImage() {
        return $this->image;
    }

    protected function getUploadDir() {
        return 'uploads/user/';
    }

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
    }

    public function getAbsolutePath() {
        return null === $this->path ? null : $this->getUploadRootDir() . $this->path;
    }

    public function getWebPath() {
        return null === $this->path ? null : $this->getUploadDir() . $this->path;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (NULL !== $this->image) {
            $this->path = uniqid($this->username . '_') . '.' . $this->getImage()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->image) {
            return;
        }

        if (isset($this->temp)) {
            try {
                unlink($this->getUploadRootDir() . $this->temp);
            } catch (\Exception $e) {
                // nothing to do
            }
            $this->temp = null;
        }

        $this->image->move($this->getUploadRootDir(), $this->path);
        $this->image = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        try {
            $path = $this->getAbsolutePath();

            if (file_exists($path) && !is_dir($path)) {
                unlink($path);
            }
        } catch (\Exception $e) {
            // nothing to do
        }
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Step
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

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Step
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * Set terms_conditions
     *
     * @param string $termsConditions
     * @return Step
     */
    public function setTermsConditions($termsConditions) {
        $this->terms_conditions = $termsConditions;

        return $this;
    }

    /**
     * Get terms_conditions
     *
     * @return string
     */
    public function getTermsConditions() {
        return $this->terms_conditions;
    }


    /**
     * Set steps
     *
     * @param integer $steps
     * @return Step
     */
    public function setSteps($steps)
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * Get steps
     *
     * @return integer 
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Step
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
}
