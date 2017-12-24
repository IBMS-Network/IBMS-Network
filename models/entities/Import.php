<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Import of products
 *
 * @ORM\Table(name="imports", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class Import extends AbstractEntity {

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
     * @ORM\Column(name="path", type="string", length=400, nullable=false)
     */
    protected $path = '';

    /**
     * @var text
     *
     * @ORM\Column(name="`desc`", type="text", length=10000, nullable=false)
     */
    protected $desc = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $created;

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
     * @return Import
     */
    public function setName( $name ){
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * @param string $path
     * @return Import
     */
    public function setPath( $path ){
        $this->path = $path;
        return $this;
    }

    /**
     * @param string $desc
     * @return Import
     */
    public function setDesc( $desc ){
        $this->desc = $desc;
        return $this;
    }

    /**
     * @return string
     */
    public function getDesc(){
        return $this->desc;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Import
     */
    public function setCreated($created)
    {
        if (is_string($created)) {
            $created = new \DateTime($created);
        } elseif (is_array($created)) {
            $created = \DateTime::__set_state($created);
        }

        $this->created = $created;

        return $this;
    }
}
