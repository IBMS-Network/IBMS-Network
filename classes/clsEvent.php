<?php

namespace classes;

use classes\core\clsDB;

class clsEvent
{

    static private $instance = NULL;
    private $__tablename__ = 'events';
    private $db = "";

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsEvent();
        }
        return self::$instance;
    }

    public function clsEvent()
    {
        $this->db = clsDB::getInstance();
    }

    public function addUser($userId)
    {
        if (isset($userId) && is_int($userId) && $userId > 0) {
            $sql = "INSERT INTO " . $this->__tablename__ .
                    "(entity_type_id, entity_id, create_date, status, direction)
					VALUES(?, ?, NOW(), 0, 0)";
            $sqlArr = array(USER_ENTITY_TYPE, $userId);
            $res = $this->db->Execute($sql, $sqlArr);

            if (isset($res)) {
                return $this->db->Insert_ID();
            }
        }

        return false;
    }

    public function addOrder($orderId)
    {
        if (isset($orderId) && is_int($orderId) && $orderId > 0) {
            $sql = "INSERT INTO " . $this->__tablename__ .
                    "(entity_type_id, entity_id, create_date, status, direction)
					VALUES(?, ?, NOW(), 0, 0)";
            $sqlArr = array(ORDER_ENTITY_TYPE, $orderId);
            $res = $this->db->Execute($sql, $sqlArr);

            if (isset($res)) {
                return $this->db->Insert_ID();
            }
        }

        return false;
    }

    public function addCompleteOrder($userId)
    {
        if (isset($userId) && is_int($userId) && $userId > 0) {
            $sql = "INSERT INTO " . $this->__tablename__ .
                    "(entity_type_id, entity_id, create_date, status, direction)
					VALUES(?, ?, NOW(), 0, 1)";
            $sqlArr = array(ORDER_COMPLETE_ENTITY_TYPE, $userId);
            $res = $this->db->Execute($sql, $sqlArr);

            if (isset($res)) {
                return $this->db->Insert_ID();
            }
        }

        return false;
    }

    public function checkFirstCompleteOrder($userId)
    {
        $sql = "
			SELECT COUNT(e.id) AS cnt
			FROM " . $this->__tablename__ . " as e
				JOIN entity_types as et ON (e.entity_type_id = et.id)
			WHERE e.status = 0 AND direction = 1
                AND e.entity_type_id = ? AND e.entity_id = ?";
        $sqlArr = array(ORDER_COMPLETE_ENTITY_TYPE, $userId);
        $res = $this->db->getRow($sql, $sqlArr);
        if (!empty($res['cnt'])) {
            return true;
        }

        return false;
    }

    public function getEventsList()
    {
        $sql = "
			SELECT e.id, et.name, e.entity_id, create_date 
			FROM " . $this->__tablename__ . " as e
				JOIN entity_types as et ON (e.entity_type_id = et.id)
			WHERE e.status = 0 AND e.direction = 0";
        return $this->db->getAll($sql);
    }

    function updateStatusFlag($eventId, $flag = 1)
    {
        $sql = "UPDATE " . $this->__tablename__ . " SET status = ? WHERE id = ?";
        $sqlArr = array((int) $flag, (int) $eventId);
        $res = $this->db->execute($sql, $sqlArr);
        return true;
    }

    function updateStatusFlagByEntityType($entityType, $entityId, $flag = 1)
    {
        $sql = "UPDATE " . $this->__tablename__ . " SET status = ? 
            WHERE entity_type_id = ? AND entity_id = ?";
        $sqlArr = array((int) $flag, (int) $entityType, (int) $entityId);
        $res = $this->db->execute($sql, $sqlArr);
        return true;
    }

}