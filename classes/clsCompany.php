<?php

namespace classes;

use classes\core\clsDB;

class clsCompany
{

    /**
     * Table name
     * @var $tableName string
     */
    private $__tablename__ = 'companies';

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
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsCompany();
        }
        return self::$instance;
    }

    /**
     * Constructor for clsStatic class
     *
     */
    public function __construct()
    {
        $this->db = clsDB::getInstance();
    }

    public function getCompaniesByUserId($userId = 0)
    {

        if (isset($userId) && is_int($userId) && $userId > 0) {

            $sql = "SELECT c.* FROM " . $this->__tablename__ . " c
					JOIN userscompanies uc ON c.id = company_id
					WHERE uc.user_id = ? AND c.status = 1";
            $sqlArr = array($userId);
            $res = $this->db->getAll($sql, $sqlArr);

            if (isset($res)) {
                return $res;
            }
        }

        return false;
    }

    public function getCompanyById($id = 0)
    {

        if (isset($id) && is_int($id) && $id > 0) {

            $sql = "SELECT c.* FROM " . $this->__tablename__ . " c
					WHERE c.id = ?";
            $sqlArr = array($id);
            $res = $this->db->getRow($sql, $sqlArr);

            if (isset($res)) {
                return $res;
            }
        }

        return false;
    }

    public function getCompanyIdByOuterId($id)
    {

        if (isset($id) && is_int($id) && $id > 0) {

            $sql = "SELECT c.id FROM " . $this->__tablename__ . " c
					WHERE c.outer_id = ?";
            $sqlArr = array($id);
            $res = $this->db->getRow($sql, $sqlArr);

            if (isset($res['id'])) {
                return (int) $res['id'];
            }
        }

        return 0;
    }

    public function addCompany($name, $info, $address, $userId)
    {

        $sql = "INSERT INTO " . $this->__tablename__ . "(name, requisite, address, reg_date)
					VALUES(?,?,?, NOW())";
        $sqlArr = array($name, $info, $address);
        $res = $this->db->Execute($sql, $sqlArr);

        if (isset($res)) {
            $sql = "INSERT INTO userscompanies(company_id, user_id)
					VALUES(?,?)";
            $sqlArr = array($this->db->Insert_ID(), $userId);
            $res2 = $this->db->Execute($sql, $sqlArr);
            if (isset($res2)) {
                return $this->db->Insert_ID();
            }
        }

        return false;
    }

    public function addCompanyFull($data, $userId)
    {

        if (!empty($data) && is_array($data) && !empty($userId)) {
            $sql = "INSERT INTO " . $this->__tablename__ . "(name, requisite, address, address_fact, reg_date, OGRN, INN,
                        payment_method, bank_name, current_account, BIK, correspondent_account, city, status)
                        VALUES(?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sqlArr = array($data['name'], '', $data['address'], $data['address_fact'], $data['OGRN'], $data['INN'],
                $data['payment_method'], $data['bank_name'], $data['current_account'],
                $data['BIK'], $data['correspondent_account'], $data['city'], $data['status']);
            $res = $this->db->Execute($sql, $sqlArr);

            if (isset($res)) {
                $sql = "INSERT INTO userscompanies(company_id, user_id)
                        VALUES(?,?)";
                $id = $this->db->Insert_ID();
                $sqlArr = array($id, $userId);
                $res2 = $this->db->Execute($sql, $sqlArr);
                if (isset($res2)) {
                    return $id;
                }
            }
        }

        return false;
    }

    public function addCompanyRaw($iOuterId, $sName, $sRegDate, $iStatus, $sAddress, $sOGRN, $sINN, $sPaymentMethod, $sBankName, $sCurrentAccount, $sBik, $sCity, $sCorrespondentAccount)
    {

        $sql = "INSERT INTO " . $this->__tablename__ . "(outer_id, name, requisite, reg_date, address,
            OGRN, INN, payment_method, bank_name, current_account, bik, city, correspondent_account, status)
					VALUES(?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $sqlArr = array($iOuterId, $sName, $sRegDate, $sAddress, $sOGRN, $sINN,
            $sPaymentMethod, $sBankName, $sCurrentAccount, $sBik, $sCity,
            $sCorrespondentAccount, $iStatus);
        $res = $this->db->Execute($sql, $sqlArr);

        return $this->db->Insert_ID();
    }

    /**
     * Update company
     * 
     * @param array $data
     * @return boolean
     */
    public function updateCompany($data = array())
    {
        if (empty($data) || !is_array($data) || empty($data))
            return false;

        $companyId = $data['id'];
        unset($data['id']);

        $sql_part = array();
        foreach ($data as $key => $value) {
            $sql_part[] = ' ' . $key . ' = "' . str_replace('', '"', $value) . '"';
        }

        if (empty($sql_part)) {
            return false;
        }

        $sql = "UPDATE " . $this->__tablename__ . " SET " . join(",", $sql_part) . " WHERE id = ?";
        $result = $this->db->Execute($sql, array($companyId));
        if (!$result)
            return false;

        return true;
    }

    /**
     * Check if company assigned to current user
     * 
     * @param int $id
     * @return boolean
     */
    public function isCurrentUserCompany($id)
    {
        $result = false;
        if (!empty($id) && is_int($id) && clsSession::getInstance()->isAuthorisedUserSession()) {

            $sql = "SELECT COUNT(c.id) cnt FROM " . $this->__tablename__ . " c
					JOIN userscompanies uc ON c.id = uc.company_id
					WHERE uc.user_id = ? AND c.id = ?";
            $sqlArr = array(clsSession::getInstance()->getUserIdSession(), $id);
            $res = $this->db->getRow($sql, $sqlArr);

            if (!empty($res['cnt'])) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Delete company
     * 
     * @param int $id
     * @return boolean
     */
    public function deleteCompany($id)
    {
        $result = false;
        if (!empty($id) && is_int($id) && $this->isCurrentUserCompany($id)) {
            $sql = "UPDATE " . $this->__tablename__ . " c
                    SET status = 0
					WHERE c.id = ?";
            $sqlArr = array($id);
            $res = $this->db->Execute($sql, $sqlArr);

            if (!empty($res)) {
                $result = true;
            }
        }

        return $result;
    }

}