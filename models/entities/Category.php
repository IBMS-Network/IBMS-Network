<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;
use URLify;
use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * Category
 *
 * @ORM\Table(name="categories", indexes={@ORM\Index(name="parent_id", columns={"parent_id", "alias", "name", "status"}), @ORM\Index(name="alias", columns={"alias"}), @ORM\Index(name="name", columns={"name"}), @ORM\Index(name="status", columns={"status"})})
 * @ORM\Entity
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=false)
     */
    private $parent_id = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description='';

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status = 1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent = null;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="categories")
     */
    private $products;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int parentId
     */
    public function getParentId()
    {
        return empty($this->parent_id) ? 0 : $this->parent_id;
    }

    /**
     * @param integer $parentId
     * @return Category
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
        return $this;
    }

    /**
     * @return string name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = !empty($description) ? $description : '';
        return $this;
    }

    /**
     * @return bool status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return Category
     */
    public function setStatus($status)
    {
        $this->status = (bool)$status;
        return $this;
    }

/**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @return Category
     */
    public function setCreated(\DateTime $created = null)
    {
        if (!$created) {
            $created = new \DateTime('now');
        }
        $this->created = $created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     * @return Category
     */
    public function setUpdated(\DateTime $updated = null)
    {
        if (!$updated) {
            $updated = new \DateTime('now');
        }
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return \entities\Category
     */
    public function getParent(){
        return $this->parent;
    }

    /**
     * @param Category $parent
     * @return Category
     */
    public function setParent(Category $parent){
        if(empty($parent) || !($parent instanceof Category)){
            $parent = null;
        } else {
            $parent->setChild($this);
        }
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildren(){
        return $this->children;
    }

    /**
     * @param Category $category
     * @return Category
     */
    public function setChild(Category $category){
        if( !$this->children->contains( $category ) ){
            $this->children->add( $category );
        }
        return $this;
    }

    /**
     * @param ArrayCollection $categories
     * @return Category
     */
    public function setChildren(ArrayCollection $categories){
        $this->children->clear();
        if( $categories ){
            foreach( $categories as $category ){
                $this->setChild( $category );
            }
        }
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProducts(){
        return $this->products;
    }
    
    /**
     * @return integer $count
     */
    public function getProductsCount(){
        $count = 0;
        $count += count($this->products);
        if(!empty($this->children)) {
            foreach($this->children as $v) {
                $count += count($v->products);
            }
        }
        
        return $count;
    }

    /**
     * @param Product $product
     * @return Category
     */
    public function setProduct(Product $product){
        if( !$this->products->contains( $product ) ){
            $this->products->add( $product );
        }
        return $this;
    }

    /**
     * @param ArrayCollection $products
     * @return Category
     */
    public function setProducts(ArrayCollection $products){
        $this->products->clear();
        if( $products ){
            foreach( $products as $product ){
                $this->setProduct( $product );
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy(){
        $parent_name = '';
        $parent = $this->getParent();
        if(!empty($parent) && $parent instanceof Category) {
            $parent_name = $parent->getName();
        }
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'parent_id' => $this->getParentId(),
            'parent_name' => $parent_name,
            'description' => $this->getDescription(),
            'status' => $this->getStatus(),
        );
    }

}
