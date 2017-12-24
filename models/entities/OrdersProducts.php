<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 *
 * @ORM\Table(name="orderproducts")
 * @ORM\Entity
 */
class OrdersProducts extends AbstractEntity
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
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    protected $productId;

    /**
     * @var double
     *
     * @ORM\Column(name="price", type="float", nullable=false)
     */
    protected $price;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    protected $quantity;
    
    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="products")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     **/
    protected $order = null;
    
    /**
     * @ORM\OneToOne(targetEntity="Product")
     **/
    protected $product;
    
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
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return double
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param $id
     * @return OrdersProducts
     */
    public function setId($id)
    {
        $this->id = (int)$id;
        return $this;
    }

    /**
     * @param $orderId
     * @return OrdersProducts
     */
    public function setOrderId($orderId)
    {
        $this->orderId = (int)$orderId;
        return $this;
    }

    /**
     * @param $productId
     * @return OrdersProducts
     */
    public function setProductId($productId)
    {
        $this->productId = (int)$productId;
        return $this;
    }

    /**
     * @param $price
     * @return OrdersProducts
     */
    public function setPrice($price)
    {
        $this->price = (double)$price;
        return $this;
    }

    /**
     * @param $quantity
     * @return OrdersProducts
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int)$quantity;
        return $this;
    }
    
    /**
     * @return \Product
     */
    public function getProduct(){
        return $this->product;
    }

    /**
     * @param $product
     * 
     * @return OrdersProducts
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }
    
    /**
     * @return \Order
     */
    public function getOrder(){
        return $this->order;
    }

    /**
     * @param \Order $order
     * 
     * @return OrdersProducts
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
        return $this;
    }
}
