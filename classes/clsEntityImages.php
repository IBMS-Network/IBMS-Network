<?php

class clsEntityImages {

	static private $instance = NULL;
	private $db = "";

	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsEntityImages();
		}
		return self::$instance;
	}
	
	public function __construct() {
		$this->db = DB::getInstance();
	}
	
	/**
	 * Clear row entityimage
	 * 
	 * @param integer $entity_type_id
	 * @param integer $item_id
	 */
	public function clearByEntityTypeIdItemId($entity_type_id, $item_id) {
		$sql = "DELETE FROM `entityimages` WHERE entity_type_id = ? AND item_id = ?";
		$sqlArr = array((int)$entity_type_id, (int)$item_id);
		$result = $this->db->Execute($sql, $sqlArr);
		return $result;
	}
	
	/**
	 * Add entityimage for item 
	 * 
     * @param integer $images
	 * @param integer $entity_type_id
	 * @param integer $item_id
	 * 
	 * @return integer
	 */
	public function addImages($images, $entity_type_id, $item_id) {
		$sql = "INSERT INTO `entityimages` (image_id, entity_type_id, item_id) VALUES ";
		$cnt = count($images);
        for($i=0; $i<$cnt; $i++) {
            $str[] = "($images[$i], $entity_type_id, $item_id)";
        }
        $sql .= implode(', ', $str);
		$result = $this->db->Execute($sql);
		
		return $result;
	} 
	
}