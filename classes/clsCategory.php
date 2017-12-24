<?php

namespace classes;

use classes\core\clsDB;

class clsCategory {
    
    private static $instance = NULL;
    private $__tablename__ = 'categories';
    private $em = "";
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsCategory();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->em = clsDB::getInstance();
    }
    
    /**
     * Get categories list by parent_id
     * 
     * @param integer $parentId
     * id parent category
     * @param integer $limit
     * count items in result rows
     * 
     * @return array
     */
    public function getAll($parentId = 0, $limit = 0) {
        $criteria = array();
        
        if ($parentId > 0) {
            $criteria['parentId'] = (int)$parentId;
        }
        
        $return = $this->em->getRepository('entities\Category')->findBy($criteria);
        array_walk($return, create_function('&$val', '$val = $val->getArrayCopy();'));
        
        return $return;
    }
    
    /**
     * Get all categories for third 
     * 
     * @param integer $parentId
     * @param integer $id
     * 
     * @return array
     */
    public function getAllForMenu($parentId = 0, $limit = 0) {
        
        $sql = "SELECT c2.* FROM `{$this->__tablename__}` c
                  JOIN `{$this->__tablename__}` c1 ON c.id = c1.parent_id
                  JOIN `{$this->__tablename__}` c2 ON c1.id = c2.parent_id
					WHERE c2.status = 1 AND c1.status = 1 and c.status = 1";
        $other = " ORDER BY c.weight DESC, c.name, c1.weight DESC, c1.name, c2.weight DESC, c2.name";
        $sqlArr = array();
        if (!empty($parentId) && is_int($parentId)) {
            $sql .= " AND c.id = ?";
            $sqlArr[] = $parentId;
        }
        if (!empty($limit)) {
            $other .= ' LIMIT ?';
            $sqlArr[] = (int)$limit;
        }
        $result = $this->db->getAll($sql . $other, $sqlArr);
        return $result;
    }
    
    /**
     * Get parents by category id
     * 
     * @param integer $id
     * 
     * @return array
     */
    public function getParents($id = 0) {
        
        $result = false;
        
        if (!empty($id) && is_int($id)) {
            $sql = "SELECT id, parent_id AS p1_id, (SELECT parent_id from `{$this->__tablename__}` where id = p1_id) AS p2_id
                        FROM `{$this->__tablename__}`
                        WHERE status = 1 AND id = ?";
            $sqlArr = array($id);
            $result = $this->db->getRow($sql, $sqlArr);
        }
        
        return $result;
    }
    
    /**
     * Get all categories by id
     * 
     * @param integer $parentId
     * @param integer $id
     * 
     * @return array
     */
    public function getAllById($id = 0, $limit = 0) {
        
        $sql = "SELECT * FROM `{$this->__tablename__}`
					WHERE status = 1
                    AND parent_id =
                        (SELECT parent_id FROM `{$this->__tablename__}` WHERE id = ?)
					ORDER BY weight DESC, name";
        $sqlArr = array($id);
        if (!empty($limit)) {
            $sql .= ' LIMIT ?';
            $sqlArr[] = $limit;
        }
        $result = $this->db->getAll($sql, $sqlArr);
        
        return $result;
    }
    
    /**
     * Get category item by id
     * 
     * @param integer $id
     * 
     * @return array
     */
    public function getCategoryById($id = 0) {
        
        $sql = "SELECT * FROM `{$this->__tablename__}` WHERE status = 1 AND id = ? LIMIT 1";
        $sqlArr = array((int)$id);
        $result = $this->db->getRow($sql, $sqlArr);
        
        return $result;
    }
    
    /**
     * Get category item by product_id
     * 
     * @param integer $productId
     * 
     * @return array
     */
    public function getCategoryByProductId($productId = 0) {
        
        $sql = "SELECT c.id, c.name category_name, c.description category_description, gp.id groip_id, p.*
						, gp.name group_name, gp.description group_description, gp.category_id, pg.group_product_id
				FROM `{$this->__tablename__}` c
				JOIN group_products gp ON gp.category_id = c.id
				JOIN productsgroups pg ON pg.group_product_id = gp.id
                JOIN products p ON p.id = pg.product_id
				WHERE p.status = 1 AND p.id = ? LIMIT 1";
        
        $sqlArr = array((int)$productId);
        $result = $this->db->getRow($sql, $sqlArr);
        
        return $result;
    }
    
    /**
     * Get list public items by ids
     * 
     * @param array $ids
     * 
     * @return array
     */
    function getPublicListByIds($ids) {
        $result = array();
        array_walk($ids, create_function('&$val', '$val = (int)$val;'));
        
        if (!empty($ids) && is_array($ids)) {
            $ids = implode(',', $ids);
            $sql = "select t.* from `{$this->__tablename__}` t where t.id IN (" . $ids . ") and t.status = 1 order by find_in_set(t.id, '" . $ids . "')";
            $result = $this->db->getAll($sql);
        }
        
        return $result;
    }
    
    /**
     * Get ID category item by name
     * and parent_id
     * 
     * @param string $name
     * @param integer $parrentId
     * 
     * @return integer
     */
    public function getCategoryIdByName($name, $parrentId = 0) {
        
        $sqlQueryPart = (!empty($parrentId) && (int)$parrentId > 0) ? (' AND parent_id = ' . (int)$parrentId . ' ') : '';
        
        $sql = "SELECT id FROM `{$this->__tablename__}` WHERE name = ? " . $sqlQueryPart . " LIMIT 1";
        $sqlArr = array($name);
        
        $result = $this->db->getRow($sql, $sqlArr);
        
        return empty($result['id']) ? false : (int)$result['id'];
    }
    
    /**
     * Get ID category item by parent ID
     * 
     * @param integer $parrentId
     * 
     * @return mixed
     */
    public function getCategoriesByParentId($parentId = 0) {
        $result = false;
        
        if (!empty($parentId)) {
            $sql = "SELECT * FROM `{$this->__tablename__}` WHERE parent_id = ? ORDER BY weight DESC, name";
            $sqlArr = array($parentId);
            
            $result = $this->db->getAll($sql, $sqlArr);
        }
        
        return $result;
    }
    
    /**
     * Get ID category item by outer_id
     * 
     * @param integer $outerId
     * 
     * @return integer
     */
    public function getCategoryIdByOuterId($outerId = 0) {
        $sql = "SELECT id FROM `{$this->__tablename__}` WHERE outer_id = ? LIMIT 1";
        $sqlArr = array($outerId);
        
        $result = $this->db->getRow($sql, $sqlArr);
        
        return empty($result['id']) ? false : (int)$result['id'];
    }
    
    /**
     * Create category item
     * 
     * @param integer $parrentId
     * id parent category item
     * @param string $name
     * name category
     * @param string $description
     * description text category
     * @param integer $status
     * activity status categories
     * 
     * @return integer
     */
    public function addCategory($parentId, $name, $description = '', $status = 0) {
        $return = 0;
        
        if ($name) {
            $category = new \entities\Category();
            $category->setParentId((int)$parentId);
            $category->setName($name);
            $category->setDescription($description);
            $category->setStatus((int)$status);
            
            $this->em->persist($category);
            $this->em->flush();
            
            $return = $category->getId();
        }
        
        return $return;
    }
    
    /**
     * Update category item
     * 
     * @param integer $id
     * @param integer $parrentId
     * @param string $name
     * @param string $description
     * @param integer $status
     */
    public function editCategory($id, $parentId, $name, $description = '', $status = 0) {
        
        $sql = "UPDATE `{$this->__tablename__}`
					SET parent_id = ?
						, name = ?
						, description = ?
                        , status = ?
		
					WHERE id = ?";
        $sqlArr = array((int)$parentId, $name, $description, (int)$status, $id);
        $result = $this->db->Execute($sql, $sqlArr);
        
        return $result;
    }
    
     /**
     * Update data category item
     * 
     * @param array $data
     * id: id category item
     * parent_id: id parent category item
     * name: name category
     * description: description text category
     * status: activity status categories
     * 
     * @return integer
     */
    public function updateCategory($data = array()) {
        $return = false;
        if (!empty($data) && is_array($data)){
            $item_id = (int)$data['id'];
            unset($data['id']);
            
            $category = $this->em->getRepository('entities\Category')->find($item_id);
            if ($category && !empty($data)){
                if (isset($data['parent_id'])) {
                    $category->setParentId((int)$data['parent_id']);
                }
                if (isset($data['name'])) {
                    $category->setName($data['name']);
                }
                if (isset($data['description'])) {
                    $category->setDescription($data['description']);
                }
                if (isset($data['status'])) {
                    $category->setStatus((int)$data['status']);
                }
                $this->em->persist($category);
                $this->em->flush();
                
                $return = true;
            }
        }
        return $return;
    }
    
    /**
     * Delete category item in DB 
     * 
     * @param integer $item_id
     * id category item
     * 
     * @return boolean
     */
    public function deleteCategory($item_id) {
        $result = false;
        if (clsCommon::isInt($item_id)) {
            $category = $this->em->getRepository('entities\Category')->find((int)$item_id);
            if ($category){
                $this->em->remove($category);
                $this->em->flush();
                $result = true;
            }
        }
        return (bool)$result;
    }

}