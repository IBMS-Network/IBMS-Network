<?php
class clsUsersCompanies {
    
    /**
	 * Table name
	 * @var $tableName string
	 */
    private $__tablename__ = 'userscompanies';

	/**
	 * Inner variable to hold own object of a class
	 * @var object $instance
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
			self::$instance = new clsUsersCompanies();
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
	
	public function clearCompanysByUserId($iUserId) {
		$sql = "DELETE FROM " . $this->__tablename__ . " WHERE  user_id = ?";
		$sqlArr = array($iUserId);
		$this->db->Execute($sql, $sqlArr);
	}
	
	public function addUserCompany($iCompanyId, $iUserId) {
        $sql = "INSERT INTO " . $this->__tablename__ . "(company_id, user_id)
			VALUES(?,?)";
		$sqlArr = array($iCompanyId, $iUserId);
		$this->db->Execute($sql, $sqlArr);
		return $this->db->Insert_ID();
	}
    
	public function addUserCompanies($companies, $userId) {
        $sql = "INSERT INTO " . $this->__tablename__ . "(company_id, user_id)
			VALUES ";
        foreach($companies as $value) {
            $value = (int)$value;
            $userId = (int)$userId;
            $str[] = "($value, $userId)";
        }
        $sql .= implode(', ', $str);
		$res = $this->db->Execute($sql);
        
		return $res;
	}
    
    public function getCompaniesCntByUserId($id) {
        $result = 0;
        
        if((int)$id > 0) {
            $sql = "SELECT COUNT(*) as cnt FROM " . $this->__tablename__ . " uc
                    JOIN companies c ON c.id = uc.company_id
                    WHERE uc.user_id = ? AND c.status = 1";
            $sqlArr = array($id);
            $res = $this->db->getRow($sql, $sqlArr);
            if($res) {
                $result = $res['cnt'];
            }
        }
        
        return $result;
    }
}