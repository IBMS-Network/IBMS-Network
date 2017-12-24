<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;
use entities\Product;

/**
 * ProductsSimilars
 *
 * @ORM\Table(name="products_similars")
 * @ORM\Entity
 */
class ProductsSimilars{

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
     * @var integer
     * 
     * @ORM\OneToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     **/
    protected $similarId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
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
     * @return integer
     */
    public function getSimilarId(){
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
