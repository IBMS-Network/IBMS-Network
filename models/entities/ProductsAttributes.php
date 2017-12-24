<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 *
 * @ORM\Table(name="productsattributes")
 * @ORM\Entity
 */
class ProductsAttributes extends AbstractEntity
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    protected $productId;

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="attribute_id", type="integer", nullable=false)
     */
    protected $attributeId;

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }
    
    /**
     * @param $productId
     * @return ProductsAttributes
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @param $attributeId
     * @return ProductsAttributes
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = (int)$attributeId;
        return $this;
    }
}
