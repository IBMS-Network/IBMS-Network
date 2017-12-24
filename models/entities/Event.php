<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="events", indexes={@ORM\Index(name="key_entity_id", columns={"entity_id"}), @ORM\Index(name="key_entity_type_id", columns={"entity_type_id"})})
 * @ORM\Entity
 */
class Event extends AbstractEntity
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
     * @var integer
     *
     * @ORM\Column(name="entity_type_id", type="integer", nullable=false)
     */
    protected $entityTypeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="entity_id", type="integer", nullable=false)
     */
    protected $entityId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    protected $createDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    protected $status = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="direction", type="boolean", nullable=false)
     */
    protected $direction = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="additional", type="string", length=50, nullable=false)
     */
    protected $additional = '';


}
