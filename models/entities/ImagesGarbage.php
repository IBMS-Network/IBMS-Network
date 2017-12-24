<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImagesGarbage
 *
 * @ORM\Table(name="images_garbage")
 * @ORM\Entity
 */
class ImagesGarbage extends AbstractEntity
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
     * @var \DateTime
     *
     * @ORM\Column(name="createdate", type="datetime", nullable=true)
     */
    protected $createdate = '1970-01-01 00:00:00';


}
