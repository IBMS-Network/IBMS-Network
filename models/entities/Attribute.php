<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute
 *
 * @ORM\Table(name="attributes", indexes={@ORM\Index(name="attribute_product_id", columns={"product_id"})})
 * @ORM\Entity
 */
class Attribute extends AbstractEntity
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="attributes")
     */
    protected $products;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=500, nullable=false)
     */
    protected $name;

    /**
     * @var text
     *
     * @ORM\Column(name="value", type="text")
     */
    protected $value;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param int $id
     * @return Attribute
     */
    public function setId($id){
        $this->id = (int) $id;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getProducts(){
        return $this->products;
    }

    /**
     * @param entities\Product $product
     * @return Attribute
     */
    public function addProduct(entities\Product $product){
        $this->products[] = $product;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @param string $name
     * @return Attribute
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }

    /**
     * @return text
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @param text $value
     * @return Attribute
     */
    public function setValue($value){
        $this->value = $value;
        return $this;
    }

}
