<?php

namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;

class clsEmailTemps
{

    /**
     * self object
     * @var clsEmailTemps
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
     * @return NULL|\classes\clsAdminEmailTemps
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsEmailTemps();
        }
        return self::$instance;
    }

    /**
     * Get Emailtemp data by ID
     * @param int $id
     * ID of the Emailtemp
     * @return boolean | \Doctrine\ORM\EntityRepository
     */
    public function getEmailtempById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('\entities\Emailtemp')->find($id);
        }
        return $res;
    }
}