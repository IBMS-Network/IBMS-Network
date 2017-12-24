<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * AclRole
 *
 * @ORM\Table(name="products_share")
 * @ORM\Entity
 */
class ProductsShare extends AbstractEntity {

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
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    protected $productId;

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }
    
    /**
     * @var \Product
     * 
     * @ORM\OneToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     **/
    protected $product;

    /**
     * @return int
     */
    public function getProductId(){
        return $this->productId;
    }
    
    /**
     * @param integer $productId
     * @return self
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        
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
     * @return Hits
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }
}
