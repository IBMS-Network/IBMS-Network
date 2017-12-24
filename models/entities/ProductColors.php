<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductColors
 *
 * @ORM\Table(name="productcolors")
 * @ORM\Entity
 */
class ProductColors extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $productId;

    /**
     * @var integer
     *
     * @ORM\Column(name="color_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $colorId;


}
