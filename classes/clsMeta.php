<?php

class clsMeta {

    static private $instance = NULL;
	private $__tablename__ = 'metas';
    private $db = "";
    private $session = "";

    /**
     * domain config array
     * @var array $config - domain config array
     */
//    private $config = array();

    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsMeta();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->db = DB::getInstance();
        $this->session = clsSession::getInstance();
    }
    
    public function getValue($type, $page, $id){
        
        if(is_string($type) && !empty($type) &&
                is_string($page) && !empty($page) &&
                is_int($id) && !empty($id)) {
            
            $sql = "SELECT value FROM pagesmetas pm
                LEFT JOIN metas m ON pm.meta_id = m.id
                LEFT JOIN page_types pt ON pm.page_type_id = pt.id
                WHERE pt.name = ? AND m.name = ? AND pm.item_id = ?
                LIMIT 1";
            $sqlArr = array($page, $type, $id);
            $res = $this->db->getRow($sql, $sqlArr);

            if($res['value']) {
                return $res['value'];
            }            
        }
        
        return '';
    }
    
    /**
     * Get id by name
     * 
     * @param string $name
     * 
     * @return integer
     */
	public function getIdByName($name) {

		$sql = "SELECT id FROM `{$this->__tablename__}` WHERE name = ? LIMIT 1";
		$sqlArr = array($name);
		$result = $this->db->getRow($sql, $sqlArr);
		
		return empty($result['id']) ? false : (int)$result['id'];
	}
}