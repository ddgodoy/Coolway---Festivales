<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * CoolwayFestivales\BackendBundle\Entity\Stage
 * @ORM\Table(name="stage")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\StageRepository")
 */
class Stage {

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
     * @ORM\ManyToMany(targetEntity="CoolwayFestivales\BackendBundle\Entity\Feast")
     * @ORM\JoinTable(name="stage_feasts",
     *     joinColumns={@ORM\JoinColumn(name="stage_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="feast_id", referencedColumnName="id")}
     * )
     */
    protected $stage_feasts;

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
     * Set name
     *
     * @param string $name
     * @return Stage
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
     * Add stage_feasts
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Feast $stageFeasts
     * @return Stage
     */
    public function addStageFeast(\CoolwayFestivales\BackendBundle\Entity\Feast $stageFeasts)
    {
        $this->stage_feasts[] = $stageFeasts;

        return $this;
    }

    /**
     * Remove stage_feasts
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Feast $stageFeasts
     */
    public function removeStageFeast(\CoolwayFestivales\BackendBundle\Entity\Feast $stageFeasts)
    {
        $this->stage_feasts->removeElement($stageFeasts);
    }

    /**
     * Get stage_feasts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStageFeasts()
    {
        return $this->stage_feasts;
    }
}
