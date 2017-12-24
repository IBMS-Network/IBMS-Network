<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;
use Sluggable\Fixture\Issue116\Country;

/**
 * AclRole
 *
 * @ORM\Table(name="brands", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class Brand extends AbstractEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=false)
     */
    protected $img = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=10000, nullable=false)
     */
    protected $description;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    protected $country = null;

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @param $name
     * @return Brand
     */
    public function setName( $name ){
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getImg(){
        return $this->img;
    }

    /**
     * @param $img
     * @return Brand
     */
    public function setImg( $img ){
        $this->img = $img;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * @param $description
     * @return Brand
     */
    public function setDescription( $description ){
        $this->description = $description;
        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry(){
        return $this->country;
    }

    /**
     * @param Country $country
     * @return Brand
     */
    public function setCountry(\entities\Country $country){
        $this->country = $country;
        return $this;
    }
}
