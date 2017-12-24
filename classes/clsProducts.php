<?php

namespace classes;

use Doctrine\ORM\Query;

class clsProducts extends clsCommonService {
    
    private static $instance = NULL;
    private $__tablename__ = 'products';
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsProducts();
        }
        return self::$instance;
    }
    
    public function __construct() {
        parent::__construct();
        $this->setServiceId($this->__tablename__);
    }
    
    /**
     * Get product data by id
     * 
     * @param integer $id
     * entity product id 
     * 
     * @return array|false
     */
    public function getProductById($id = 0) {
        $result = $this->em->getRepository('entities\Product')->findOneBy(array('id' => (int)$id, 'status' => 1))->getArrayCopy();
        return $result;
    }
    
    /**
     * Get all categories by id and parent_id
     * 
     * @param integer $categoryId
     * id parent category  
     * @param integer $limit
     * count items in rows
     * 
     * @return array
     */
    public function getAll($categoryId = 0, $limit = 0) {
        $return = $this->em->getRepository('entities\Product')->findBy(array('categoryId' => (int)$categoryId, 'status' => 1), array());
        array_walk($return, create_function('&$val', '$val = $val->getArrayCopy();'));
        
        return $return;
    }
    
    /**
     * Create product item
     * 
     * @param integer $categoryId
     * @param string $name
     * @param string $description
     * @param integer $price
     * @param integer $status
     * 
     * @return integer
     */
    function addProduct($categoryId, $name, $description = '', $price = 0, $status = 1) {
        $return = 0;
        
        if ($name) {
            $product = new \entities\Product();
            $product->setCategoryId($categoryId);
            $product->setName($name);
            $product->setDescription($description);
            $product->setPrice((int)$price);
            $product->setStatus((int)$status);
            
            $this->em->persist($product);
            $this->em->flush();
            
            $return = $product->getId();
        }
        
        return $return;
    }
    
    /**
     * Update product item
     * 
     * @param integer $id
     * @param integer $categoryId
     * @param string $name
     * @param string $description
     * @param integer $price
     * @param integer $status
     */
    public function editProduct($id, $categoryId, $name, $description, $price, $status) {
        
        $sql = "UPDATE `products`
					SET category_id = ?
						, name = ?
						, description = ?
						, status = ?
		
					WHERE id = ?";
        $sqlArr = array($categoryId, $name, $description, $status, $id);
        $result = $this->db->Execute($sql, $sqlArr);
        
        return $result;
    }
    
    /**
     * Update product item
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function updateProduct($data = array()) {
        $return = false;
        if (!empty($data) && is_array($data)){
            $product_id = (int)$data['id'];
            unset($data['id']);
            
            $product = $this->em->getRepository('entities\Product')->find($product_id);
            if ($product && !empty($data)){
                if (isset($data['category_id'])) {
                    $product->setCategoryId((int)$data['category_id']);
                }
                if (isset($data['name'])) {
                    $product->setName($data['name']);
                }
                if (isset($data['description'])) {
                    $product->setDescription($data['description']);
                }
                if (isset($data['price'])) {
                    $product->setPrice((int)$data['price']);
                }
                if (isset($data['status'])) {
                    $product->setStatus((int)$data['status']);
                }
                $this->em->persist($product);
                $this->em->flush();
                
                $return = true;
            }
        }
        return $return;
    }
    
    /**
     * Delete product item in DB 
     * 
     * @param integer $item_id
     * id product item
     * 
     * @return boolean
     */
    public function deleteProduct($item_id) {
        $result = false;
        if (clsCommon::isInt($item_id)) {
            $product = $this->em->getRepository('entities\Product')->find((int)$item_id);
            if ($product){
                $this->em->remove($product);
                $this->em->flush();
                $result = true;
            }
        }
        return (bool)$result;
    }
    
    public function getCountForCategory($categoryId) {
        $categoryId = (int)$categoryId;
        
        $qb = $this->em->createQueryBuilder();
        $query = $qb
            ->select('COUNT(p.id)') 
            ->from('entities\Product', 'c')
            ->where('c.category_id = :id')
            ->groupBy('c.id')
            ->setParameter('id', $categoryId)
            ->getQuery();
        return $query->getScalarResult();
    }

}