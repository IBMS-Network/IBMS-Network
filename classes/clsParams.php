<?php
namespace classes;

use classes\core\clsDB;

class clsParams
{

    /**
     * self object
     * @var clsParams
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Constructorof the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        $this->em = clsDB::getInstance();
    }

    /**
     * Singleton
     * @return NULL|\classes\clsParams
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsParams();
        }
        return self::$instance;
    }

    /**
     * Get Param value by Name
     * @param string $name
     * Name of the Param
     * @return boolean | string
     */
    public function getParamValueByName($name)
    {
        $res = false;
        if (!empty($name)) {
            $res = $this->em->getRepository('\entities\Param')->findOneByName($name);
            if($res) {
                $res = $res->getValue();
            }
        }
        return $res;
    }
}