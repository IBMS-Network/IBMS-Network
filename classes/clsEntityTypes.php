<?php
class clsEntityTypes {

	static private $instance = NULL;
	private $__tablename__ = 'entity_types';
	private $db = "";

	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsEntityTypes();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->db = DB::getInstance();
	}
	
	public function getIdByName($name) {

		$sql = "SELECT id FROM `{$this->__tablename__}` WHERE name = ? LIMIT 1";
		$sqlArr = array($name);
		$result = $this->db->getRow($sql, $sqlArr);
		
		return empty($result['id']) ? false : (int)$result['id'];
	}
	
}