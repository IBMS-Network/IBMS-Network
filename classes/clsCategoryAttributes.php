<?php
namespace classes;

use classes\core\clsDB;

class clsCategoryAttributes {

	static private $instance = NULL;
	private $__tablename__ = 'categoryattributes';
	private $db = "";

	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsCategoryAttributes();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->db = clsDB::getInstance();
	}
	
	/**
	 * Get id by params
	 * 
	 * @param integer $category_id
	 * @param integer $attribute_id
	 * @param integer $ordinary
	 */
	public function getIdByParams($category_id, $attribute_id, $ordinary) {
		$sql = "SELECT id FROM `{$this->__tablename__}` WHERE category_id = ? AND attribute_id = ? AND ordinary = ? LIMIT 1";
		$sqlArr = array($category_id, $attribute_id, $ordinary);
		$result = $this->db->getRow($sql, $sqlArr);
		
		return empty($result['id']) ? false : (int)$result['id'];
	}
	
	/**
	 * Clear joins by category_id
	 * 
	 * @param integer $category_id
	 */
	public function clearByCategoryId($category_id) {
		$sql = "DELETE FROM `{$this->__tablename__}` WHERE category_id = ?";
		$sqlArr = array($category_id);
		$result = $this->db->Execute($sql, $sqlArr);
		return $result;
	}
	
	/**
	 * Add join category and attribute
	 * 
	 * @param integer $category_id
	 * @param integer $attribute_id
	 * @param integer $ordinary
	 * 
	 * @return integer
	 */
	public function addAttribute($category_id, $attribute_id, $ordinary) {
		$sql = "INSERT INTO `{$this->__tablename__}` (category_id, attribute_id, ordinary) VALUES (?, ?, ?)";
		$sqlArr = array($category_id, $attribute_id, $ordinary);
		$result = $this->db->getRow($sql, $sqlArr);
		
		return $this->db->Insert_ID();
	}
	
	/**
	 * Set select param
	 *   for row by id
	 * 
	 * @param integer $id
	 */
	public function setIsSelect($id) {
		$sql = "UPDATE categoryattributes SET isselect = 1 WHERE id = ? ;";
		$sqlArr = array((int)$id);
		$result = $this->db->Execute($sql, $sqlArr);
		return $result;
	}
	
	
	/**
	 * Check select param
	 *   for row by attribute_id
	 * 
	 * @param integer $attributeId
	 */
	public function isSelectByAtributeId($attributeId) {
		$sql = "SELECT id FROM categoryattributes WHERE isselect = 1 AND attribute_id = ? ;";
		$sqlArr = array((int)$attributeId);
		$result = $this->db->getRow($sql, $sqlArr);
		
		return empty($result['id']) ? false : (int)$result['id'];
	}
	
}