<?php

namespace classes;

use classes\core\clsDB;

class clsCodes
{

    static private $instance = NULL;
    private $db = "";

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsCodes();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->db = clsDB::getInstance();
    }

    /**
     * Get codeout_id by product_id and name
     * 
     * @param integer $product_id
     * @param string $name
     */
    public function getIdByProductIdCodeout($product_id, $name)
    {
        $sql = "SELECT * FROM `codes` WHERE product_id = ? AND name = ? LIMIT 1";
        $sqlArr = array((int) $product_id, $name);
        $result = $this->db->getRow($sql, $sqlArr);

        return empty($result['id']) ? false : (int) $result['id'];
    }

    /**
     * Clear row codeout for product_id
     * 
     * @param integer $product_id
     */
    public function clearByProductId($product_id)
    {
        $sql = "DELETE FROM `codes` WHERE product_id = ?";
        $sqlArr = array((int) $product_id);
        $result = $this->db->Execute($sql, $sqlArr);
        return $result;
    }

    /**
     * Add codeout for product 
     * 
     * @param integer $product_id
     * @param string $name
     * 
     * @return integer
     */
    public function addCodeout($product_id, $name)
    {
        $sql = "INSERT INTO `codes` (product_id, name) VALUES (?, ?)";
        $sqlArr = array((int) $product_id, $name);
        $result = $this->db->getRow($sql, $sqlArr);

        return $this->db->Insert_ID();
    }

}