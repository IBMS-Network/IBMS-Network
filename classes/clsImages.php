<?php

class clsImages {

	static private $instance = NULL;
	private $__tablename__ = 'images';
	private $db = "";

	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsImages();
		}
		return self::$instance;
	}

	public function clsImages() {
		$this->db = DB::getInstance();
	}
    
	/**
	 * Get ID image item by name
	 * 
	 * @param integer $name
	 * 
	 * @return integer
	 */
	public function getImageIdByName($name = '') {
		$sql = "SELECT id FROM `{$this->__tablename__}` WHERE name = ? LIMIT 1";
		$sqlArr = array($name);
		
		$result = $this->db->getRow($sql, $sqlArr);
		
		return empty($result['id']) ? false : (int)$result['id'];
	}
	
	/**
	 * Create image item
	 * 
	 * @param string $name
	 * @param string $path
	 * 
	 * @return integer
	 */
	public function addImage($name, $path = '') {

		$sql = "INSERT INTO `{$this->__tablename__}` (name, path) VALUES (?, ?)";
		$sqlArr = array($name, $path);
		$result = $this->db->getRow($sql, $sqlArr);
		
		return $this->db->Insert_ID();
	}
	
	/**
	 * Update image item
	 * 
	 * @param integer $id
	 * @param string $name
	 * @param string $path
	 */
	public function editImage($id, $name = '', $path = '') {

		$sql = "UPDATE `{$this->__tablename__}`
					SET name = ?
						, path = ?
					WHERE id = ?";
		$sqlArr = array($name, $path, $id);
		$result = $this->db->Execute($sql, $sqlArr);
		
		return $result;
	}
    
    public function getImagesByProductIds($ids, $entityType = 'product') {
        $result = false;
        
        if(!empty($ids) && is_array($ids) && !empty($entityType)) {
            $sql = "SELECT
                      i.*, e.item_id
                    FROM images i
                      JOIN entityimages e
                        ON e.image_id = i.id
                      JOIN entity_types et
                        ON et.id = e.entity_type_id
                      JOIN productsgroups p
                        ON p.group_product_id = e.item_id
                    WHERE et.name = ? AND p.product_id IN (" . join(',', $ids) . ")
                    GROUP BY e.item_id";
            $result = $this->db->Execute($sql, $entityType);
        }
        
        return $result;
    }
}