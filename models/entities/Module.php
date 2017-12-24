<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Modules
 *
 * @ORM\Table(name="modules")
 * @ORM\Entity
 */
class Module extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param $id
     * @return self
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }

}
