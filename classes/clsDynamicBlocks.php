<?php

namespace classes;

use classes\core\clsDB;

class clsDynamicBlocks
{

    static private $instance = NULL;
    private $__tablename__ = 'static_blocks';
    private $db = "";

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsDynamicBlocks();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->db = clsDB::getInstance();
    }

    /**
     * Get block content by id
     *   
     * @param integer $id
     * 
     * @return string
     */
    public function getBlockById($id)
    {
        $content = '';
        if (!empty($id)) {
            $sql = "SELECT `content` FROM `" . $this->__tablename__ . "` WHERE id = ?";
            $sqlArr = array((int) $id);
            $res = $this->db->getRow($sql, $sqlArr);
            if ($res) {
                $content = $res['content'];
            }
        }

        return $content;
    }

    /**
     * Get ID block item by outer id
     *   
     * @param integer $outerId
     * 
     * @return integer
     */
    public function getBlockIdByOuterId($outerId)
    {

        $sql = "SELECT id FROM `{$this->__tablename__}` WHERE outer_id = ? LIMIT 1";
        $sqlArr = array($outerId);

        $result = $this->db->getRow($sql, $sqlArr);

        if (!empty($result['id'])) {
            return (int) $result['id'];
        }

        return false;
    }

    /**
     * Create block item
     * 
     * @param integer $outerId
     * @param string $title
     * @param string $description
     * @param string $content
     * @param string $createDate
     * @param integer $status
     * 
     * @return integer
     */
    public function addBlock($outerId, $title, $description, $content, $createDate, $status)
    {

        $sql = "INSERT INTO `{$this->__tablename__}` (outer_id, outer_key, description,
            content, create_date, status) VALUES (?, ?, ?, ?, ?, ?)";
        $sqlArr = array($outerId, $title, $description, $content, $createDate, $status);
        $result = $this->db->getRow($sql, $sqlArr);

        return $this->db->Insert_ID();
    }

    /**
     * Update block item
     * 
     * @param integer $outerId
     * @param string $title
     * @param string $description
     * @param string $content
     * @param string $createDate
     * @param integer $status
     */
    public function editBlock($outerId, $title, $description, $content, $createDate = 'NOW()', $status = 1)
    {

        $sql = "UPDATE `{$this->__tablename__}`
					SET outer_key = ?
						, description = ?
						, content = ?
						, create_date = ?
						, status = ?
		
					WHERE outer_id = ?";
        $sqlArr = array($title, $description, $content, $createDate, $status,
            $outerId);
        $result = $this->db->Execute($sql, $sqlArr);

        return $result;
    }

}