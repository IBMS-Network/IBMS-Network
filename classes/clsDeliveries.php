<?php

namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;

class clsDeliveries
{

    /**
     * self object
     * @var clsDeliveries
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Constructor of the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        $this->em = clsDB::getInstance();
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdminDeliveries
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsDeliveries();
        }
        return self::$instance;
    }

    /**
     * Get Delivery data by ID
     * @param int $id
     * ID of the Param
     * @return boolean | \Doctrine\ORM\EntityRepository
     */
    public function getDeliveryById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('\entities\Delivery')->find($id);
        }
        return $res;
    }
    
    /**
     * Get Deliveries data
     * 
     * @return boolean | \Doctrine\ORM\EntityRepository
     */
    public function getDeliveries()
    {
        return $this->em->getRepository('\entities\Delivery')->findAll();
    }
}