<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deliveries
 *
 * @ORM\Table(name="deliveries", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class Delivery extends AbstractEntity {

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
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=400, nullable=false)
     */
    protected $value;

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
     * @return Delivery
     */
    public function setName( $name ){
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @param string $value
     * @return Delivery
     */
    public function setValue( $value ){
        $this->value = $value;
        return $this;
    }

}
