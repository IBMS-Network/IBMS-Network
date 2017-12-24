<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 *
 * @ORM\Table(name="servicesimages", indexes={@ORM\Index(name="service_id", columns={"service_id", "item_id", "image_id"})})
 * @ORM\Entity
 */
class ServiceImage extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="service_id", type="integer", nullable=false)
     */
    protected $serviceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=false)
     */
    protected $itemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="image_id", type="integer", nullable=false)
     */
    protected $imageId;

    /**
     * @return int
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @return int
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * @param $serviceId
     * @return ServiceImage
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = (int)$serviceId;
        return $this;
    }
    
    /**
     * @param $itemId
     * @return ServiceImage
     */
    public function setItemId($itemId)
    {
        $this->itemId = (int)$itemId;
        return $this;
    }

    /**
     * @param $imageId
     * @return ServiceImage
     */
    public function setImageId($imageId)
    {
        $this->imageId = (int)$imageId;
        return $this;
    }
}
