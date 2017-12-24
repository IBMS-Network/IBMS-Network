<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntityType
 *
 * @ORM\Table(name="entity_types")
 * @ORM\Entity
 */
class EntityType extends AbstractEntity
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
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    protected $name;


}
