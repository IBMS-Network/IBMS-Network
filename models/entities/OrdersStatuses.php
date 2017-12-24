<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * Service
 *
 * @ORM\Table(name="ordersstatuses")
 * @ORM\Entity
 */
class OrdersStatuses extends AbstractEntity
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
     * @ORM\Column(name="order_id", type="integer", nullable=false)
     */
    protected $orderId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="status_id", type="integer", nullable=false)
     */
    protected $statusId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="statuses")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order = null;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return integer
     */
    public function getStatusId()
    {
        return $this->statusId;
    }
    
    /**
     * @param $id
     * @return OrdersStatuses
     */
    public function setId($id)
    {
        $this->id = (int)$id;
        return $this;
    }

    /**
     * @param $orderId
     * @return OrdersStatuses
     */
    public function setOrderId($orderId)
    {
        $this->orderId = (int)$orderId;
        return $this;
    }

    /**
     * @param $statusId
     * @return OrdersProducts
     */
    public function setStatusId($statusId)
    {
        $this->statusId = (int)$statusId;
        return $this;
    }
    
    /**
     * @return Order
     */
    public function getOrder(){
        return $this->order;
    }

    /**
     * @param Order $order
     * 
     * @return OrdersStatuses
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
        return $this;
    }
    
    
    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param \DateTime $modified
     *
     * @return OrdersStatuses
     */
    public function setUpdated($modified)
    {
        if (is_string($modified)) {
            $modified = new \DateTime($modified);
        } elseif (is_array($modified)) {
            $modified = \DateTime::__set_state($modified);
        }

        $this->updated = $modified;

        return $this;
    }
}
