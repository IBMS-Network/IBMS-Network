<?php
class clsSynonyms {

	static private $instance = NULL;
	private $__tablename__ = 'synonyms';
	private $db = "";

	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsSynonyms();
		}
		return self::$instance;
	}
	
	public function clsSynonyms() {
		$this->db = DB::getInstance();
	}
	
	/**
	 * get all wordforms
	 *   for generated wordform.txt (Sphinx)
	 * 
	 * @return array
	 */
	public function getWordFormsList() {

        $sql = "(SELECT s.name wordform, c.name word
        			FROM `{$this->__tablename__}` s
            		JOIN categories c ON c.id = s.item_id
            		JOIN entity_types et ON et.id = s.entity_type_id
            		WHERE et.name = 'category')
            	UNION ALL
            	(SELECT s.name wordform, c.name word
        			FROM `{$this->__tablename__}` s
            		JOIN products c ON c.id = s.item_id
            		JOIN entity_types et ON et.id = s.entity_type_id
            		WHERE et.name = 'product')
            		";
        $result = $this->db->getAll($sql);

        return $result;
	}
	
	/**
	 * Clear synonim for items
	 * 
	 * @param integer $iEntityTypeId
	 * @param integer $iItemId
	 */
	public function clearByCategoryId($iEntityTypeId, $iItemId) {
		$sql = "DELETE FROM `{$this->__tablename__}` WHERE entity_type_id = ? AND item_id = ?";
		$sqlArr = array((int)$iEntityTypeId, (int)$iItemId);
		$result = $this->db->Execute($sql, $sqlArr);
		return $result;
	}
	
	/**
	 * Add synonim for use in search
	 * 
	 * @param integer $iEntityTypeId
	 * @param integer $iItemId
	 * @param string $sName
	 * 
	 * @return integer
	 */
	public function addSynonym($iEntityTypeId, $iItemId, $sName) {
		$sql = "INSERT INTO `{$this->__tablename__}` (entity_type_id, item_id, name) 
						VALUES (?, ?, ?)";
		$sqlArr = array((int)$iEntityTypeId, (int)$iItemId, $sName);
		$res = $this->db->Execute($sql, $sqlArr);
		return $this->db->Insert_ID();
	}
    
    /**
	 * Add synonim for use in search
	 * 
	 * @param integer $iEntityTypeId
	 * @param integer $iItemId
	 * @param array $aName
	 * 
	 * @return integer
	 */
	public function addSynonyms($iEntityTypeId, $iItemId, $aNames) {
		$sql = "INSERT INTO `{$this->__tablename__}` (entity_type_id, item_id, name) 
						VALUES ";
        $params = array();
        foreach($aNames as $v) {
            $params[] = "(" . $iEntityTypeId . ", " . $iItemId . ", '". $v['synonym'] . "')";
        }
        $sql .= join(', ', $params);
		$res = $this->db->Execute($sql);
        
		return true;
	}
}