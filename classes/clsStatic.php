<?php

/**
 * clsUser class perform methods and action for project Users
 *
 * @author     AnatolikFPMI <anatolikfpmi2@gmail.com>
 * @version    1.1 2010-02-15
 */
class clsStatic {

	/**
	 * Inner variable to hold own object of a class
	 * @var object $instance - object of the clsContentVideo
	 */
	static private $instance = NULL;

	/**
	 * variable of DB class , present DB connect
	 * @var $db object
	 */
	private $db = "";

	/**
	 * getInstance function create or return alreadty exists object of this class
	 *
	 * @return object $instance - object of this class
	 */
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsStatic();
		}
		return self::$instance;
	}

	/**
	 * Constructor for clsStatic class
	 *
	 */
	public function __construct() {
		$this->db = DB::getInstance();
	}
	
	public function getAll() {
			
        $sql = "SELECT id, title FROM static_pages WHERE status = 1 ORDER BY title";
        $res = $this->db->getAll($sql);

        if(isset($res)) {
            return $res;
        }
		
		return false;
	}
    
    public function getContent($pageId = 0) {
		if(isset($pageId) && is_int($pageId) && $pageId > 0) {
			
			$sql = "SELECT * FROM static_pages WHERE status = 1 AND id = ?";
			$sqlArr = array($pageId);
			$res = $this->db->getRow($sql, $sqlArr);
			
			if(isset($res)) {
				return $res;
			}
			
		}
		
		return false;
	}

	public function getPageIdByOuterId($outerId) {

		$sql = "SELECT id FROM static_pages WHERE outer_id = ? LIMIT 1";
		$sqlArr = array((int)$outerId);
		$res = $this->db->getRow($sql, $sqlArr);

		return empty($res['id']) ? 0 : (int)$res['id'];
	}    
	
	public function createPageRaw($iOuterId, $sTitle, $sDescription, $sContent, $dCreateDate, $iStatus) {
		$sql = "INSERT INTO static_pages (outer_id, title, description, content, status, create_date) VALUES (?, ?, ?, ?, ?, ?)";
		$sqlArr = array($iOuterId, $sTitle, $sDescription, $sContent, $iStatus, $dCreateDate);
		$res = $this->db->Execute($sql, $sqlArr);
		return $this->db->Insert_ID();
	}
}