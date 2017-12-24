<?php
namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="products", indexes={@ORM\Index(name="category_id", columns={"category_id"})})
 * @ORM\Entity
 */
class Product extends AbstractEntity
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
     * @ORM\Column(name="category_id", type="integer", nullable=true)
     */
    protected $category_id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="products")
     * @ORM\JoinTable(name="categories_products")
     */
    protected $categories;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=300, nullable=false)
     */
    protected $description = '';

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", nullable=false)
     */
    protected $content = '';

    /**
     * @var string
     *
     * @ORM\Column(name="articul", type="string", length=50, nullable=false)
     */
    protected $articul;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=false)
     */
    protected $code;

    /**
     * @var double
     *
     * @ORM\Column(name="price", type="float", nullable=false)
     */
    protected $price = 0;
    
    /**
     * @var double
     *
     * @ORM\Column(name="price2", type="float", nullable=false)
     */
    protected $price2 = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    protected $status = 1;

    /**
     * @var date
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $created;

    /**
     * @var \entities\Model
     *
     * @ORM\ManyToOne(targetEntity="Model")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     * })
     */
    protected $model;

    /**
     * @var \entities\Brand
     *
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     * })
     */
    protected $brand;

//    /**
//     * @var \entities\Discount
//     *
//     * @ORM\ManyToOne(targetEntity="Discount")
//     * @ORM\JoinColumns({
//     *   @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
//     * })
//     */
//    protected $discount;

    /**
     * @var \entities\Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    protected $country = null;
    
    /**
     * @var \entities\Texture
     *
     * @ORM\ManyToOne(targetEntity="Texture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="texture_id", referencedColumnName="id")
     * })
     */
    protected $texture = null;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=false)
     */
    protected $img = '';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     *
     * @ORM\ManyToMany(targetEntity="Attribute", inversedBy="products", cascade={"remove"})
     * @ORM\JoinTable(name="productsattributes",
     * joinColumns={
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * },
     * inverseJoinColumns={
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     * }
     * )
     */
    protected $attributes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Color", inversedBy="products", cascade={"persist"})
     * @ORM\JoinTable(name="productcolors",
     * joinColumns={
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * },
     * inverseJoinColumns={
     * @ORM\JoinColumn(name="color_id", referencedColumnName="id")
     * }
     * )
     */
    protected $colors;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="availability", type="integer", nullable=false)
     */
    protected $availability = 1;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\ManyToMany(targetEntity="Product")
     * @ORM\JoinTable(name="products_similars",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="similar_id", referencedColumnName="id", unique=true)}
     *  )
     **/
    protected $similars;
    
    /**
     * Constructor
     */
    public function __construct(){
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->similars = new \Doctrine\Common\Collections\ArrayCollection();
        $this->colors = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param integer $categoryId
     * @return Product
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

     /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }
    
    /**
     * @return float
     */
    public function getPrice2()
    {
        return $this->price2;
    }

    /**
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = (float)$price;
        return $this;
    }
    
    /**
     * @param float $price2
     * @return Product
     */
    public function setPrice2($price2)
    {
        $this->price2 = (float)$price2;
        return $this;
    }

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $status
     * @return Product
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getArticul()
    {
        return $this->articul;
    }

    /**
     * @param string $articul
     * @return Product
     */
    public function setArticul($articul)
    {
        $this->articul = $articul;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Product
     */
    public function setCode($code)
    {
        $this->code = strlen($code) > 50 ? substr($code,0,50) : $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Product
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return date
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @param \DateTime $created
     * @return Product
     */
    public function setCreateDate(\DateTime $created = null)
    {
        if (!$created) {
            $created = new \DateTime('now');
        }
        $this->createDate = $created;
        return $this;
    }

    /**
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param string $img
     * @return Product
     */
    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }

    /**
     * @return \entities\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param \entities\Model $model
     * @return Product
     */
    public function setModel(\entities\Model $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return \entities\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param \entities\Brand $brand
     * @return Product
     */
    public function setBrand(\entities\Brand $brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return \entities\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param \entities\Country $country
     * @return Product
     */
    public function setCountry(\entities\Country $country)
    {
        $this->country = $country;
        return $this;
    }
    
    /**
     * @return \entities\Texture
     */
    public function getTexture()
    {
        return $this->texture;
    }

    /**
     * @param \entities\Texture $texture
     * @return Product
     */
    public function setTexture(\entities\Texture $texture)
    {
        $this->texture = $texture;
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getAttributes(){
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return Product
     */
    public function setAttributes( array $attributes ){
        $this->attributes->clear();
        if( $attributes ){
            foreach( $attributes as $attribute ){
                $this->setAttribute( $attribute );
            }
        }
        return $this;
    }

    /**
     * @param \entities\Attribute $attribute
     * @return Product
     */
    public function setAttribute( \entities\Attribute $attribute ){
        if( !$this->attributes->contains( $attribute ) ){
            $this->attributes->add( $attribute );
        }
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return Product
     */
    public function setCategories(array $categories)
    {
        $this->categories->clear();
        if( $categories ){
            foreach( $categories as $category ){
                $this->setCategory( $category );
            }
        }
        return $this;
    }
    
    /**
     * @param \entities\Category $category
     * @return Product
     */
    public function setCategory( \entities\Category $category ){
        if( !$this->categories->contains( $category ) ){
            $this->categories->add( $category );
        }
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getSimilars(){
        return $this->similars;
    }

    /**
     * @return Product
     */
    public function clearSimilars(){
        $this->similars->clear();
        return $this;
    }
    
    /**
     * @param array $similars
     * @return Product
     */
    public function setSimilars( array $similars ){
        $this->similars->clear();
        if( $similars ){
            foreach( $similars as $similar ){
                $this->setSimilar( $similar );
            }
        }
        return $this;
    }

    /**
     * @param \entities\Product $similar
     * @return Product
     */
    public function setSimilar(\entities\Product $similar ){
        if( !$this->similars->contains( $similar ) ){
            $this->similars->add( $similar );
        }
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getColors(){
        return $this->colors;
    }

    /**
     * @param array $colors
     * @return Product
     */
    public function setColors( array $colors ){
        $this->colors->clear();
        if( $colors ){
            foreach( $colors as $color ){
                $this->setColor( $color );
            }
        }
        return $this;
    }

    /**
     * @param \entities\Color $color
     * @return Product
     */
    public function setColor(\entities\Color $color ){
        if( !$this->colors->contains( $color ) ){
            $this->colors->add( $color );
        }
        return $this;
    }

    /**
     * Method to get attributes list in Array notation
     * @return array
     */
    public function getAttributesInArray(){
        $attributes = array();
        if( $this->attributes ){
            foreach( $this->attributes->toArray() as $attribute ){
                $attributes[$attribute->getId()]['name'] = $attribute->getName();
                $attributes[$attribute->getId()]['value'] = $attribute->getValue();
            }
        }
        return $attributes;
    }
    
    /**
     * Get availability
     *
     * @return integer
     */
    function getAvailability()
    {
        return $this->availability;
    }

    /**
     * Set availability
     *
     * @param integer $availability
     * @return Product
     */
    function setAvailability($availability)
    {
        $this->availability = $availability;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getArrayCopy(){
        return array(
            'id' => $this->getId(),
            'img' => $this->getImg() ? $this->getImg() : 'images/default-product-big.jpg',
            'brand' => array(
                'name' => $this->getBrand()->getName(),
            ),
            'name' => $this->getName(),
            'availability' => $this->getAvailability(),
            'price' => $this->getPrice(),
            'price2' => $this->getPrice2(),
            'articul' => $this->getArticul()
        );
    }
}
