<?php

namespace engine;

use engine\clsSysAcl;
use classes\core\clsDB;
use Doctrine\ORM\Query\Expr\Join;

class clsMobAcl extends clsSysAcl
{

    /**
     * Inner variable to hold own object of a class
     * @var object $instance - object of the clsSysAcl
     */
    private static $instance = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsMobAcl();
        }
        return self::$instance;
    }

    /**
     * Constructor for clsMobAcl class
     */
    public function __construct()
    {
        parent::__construct();
        $this->em = clsDB::getInstance();
    }

    /**
     * Select all permissions record by role
     * 	for module mobile
     *
     * @param string $roleName
     *
     * @return array
     */
    function GetPermissionsByRole($roleName = '')
    {
        $result = $this->em->createQueryBuilder()
            ->select('ap.id as perm_id, ap.name as perm_name')
            ->from('entities\AclMobilePermission', 'ap')
            ->innerJoin('entities\AclMobilePermissionrole', 'apr', 'WITH', 'ap.id=apr.permId')
            ->innerJoin('entities\AclMobileRole', 'ar', 'WITH', 'ar.id=apr.roleId')
            ->where('ar.name = :roleName')
            ->setParameter('roleName', addslashes($roleName))
            ->getQuery()
            ->getResult();

        return $result;
    }

}