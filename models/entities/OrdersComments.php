<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * Service
 *
 * @ORM\Table(name="orderscomments")
 * @ORM\Entity
 */
class OrdersComments extends AbstractEntity
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
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="orderComments")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order = null;
    
    /**
     * @ORM\ManyToOne(targetEntity="Admin", inversedBy="comments")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="id")
     */
    protected $admin = null;
    
    /**
     * var string
     * 
     * @ORM\Column(type="string", nullable=false)
     */
    protected $comment;

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
     * @param $id
     * @return OrdersComments
     */
    public function setId($id)
    {
        $this->id = (int)$id;
        return $this;
    }

    /**
     * @param $orderId
     * @return OrdersComments
     */
    public function setOrderId($orderId)
    {
        $this->orderId = (int)$orderId;
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
     * @return OrdersComments
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
        return $this;
    }
    
    /**
     * @return Admin
     */
    function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @return string
     */
    function getComment()
    {
        return $this->comment;
    }

    /**
     * @param Admin $admin
     * 
     * @return OrdersComments
     */
    function setAdmin($admin)
    {
        $this->admin = $admin;
        return $this;
    }

    /**
     * @param string $comment
     * 
     * @return OrdersComments
     */
    function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }
}
