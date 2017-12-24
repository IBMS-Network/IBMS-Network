<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="images", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name", "type", "width", "height", "th"}), @ORM\UniqueConstraint(name="full_key", columns={"full_key"})})
 * @ORM\Entity
 */
class Image extends AbstractEntity
{
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
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    protected $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=false)
     */
    protected $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=false)
     */
    protected $height;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ts", type="datetime", nullable=false)
     */
    protected $ts = 'CURRENT_TIMESTAMP';

    /**
     * @var boolean
     *
     * @ORM\Column(name="th", type="boolean", nullable=false)
     */
    protected $th;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="bigint", nullable=false)
     */
    protected $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="full_key", type="string", length=255, nullable=false)
     */
    protected $fullKey;

    /**
     * @var string
     *
     * @ORM\Column(name="orig_name", type="text", nullable=false)
     */
    protected $origName;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return DateTime
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
     * @return int
     */
    public function getTh()
    {
        return $this->th;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return string
     */
    public function getFullKey()
    {
        return $this->fullKey;
    }

    /**
     * @return string
     */
    public function getOrigName()
    {
        return $this->origName;
    }

    /**
     * @param $id
     * @return Image
     */
    public function setId($id)
    {
        $this->id = (int)$id;
        return $this;
    }

    /**
     * @param $name
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $type
     * @return Image
     */
    public function setType($type)
    {
        $this->type = (int)$type;
        return $this;
    }
    
    /**
     * @param $width
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @param $height
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = (int)$height;
        return $this;
    }

    /**
     * @param $ts
     * @return Image
     */
    public function setTs(\DateTime $ts)
    {
        $this->ts = $ts;
        return $this;
    }

    /**
     * @param $th
     * @return Image
     */
    public function setTh($th)
    {
        $this->th = $th;
        return $this;
    }

    /**
     * @param $weight
     * @return Image
     */
    public function setWeight($weight)
    {
        $this->weight = (int)$weight;
        return $this;
    }
    
    /**
     * @param $fullKey
     * @return Image
     */
    public function setFullKey($fullKey)
    {
        $this->fullKey = $fullKey;
        return $this;
    }

    /**
     * @param $origName
     * @return Image
     */
    public function setOrigName($origName)
    {
        $this->origName = $origName;
        return $this;
    }
}
