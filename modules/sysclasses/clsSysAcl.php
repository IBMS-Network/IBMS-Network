<?php

namespace engine;

use classes\core\clsDB;
use classes\core\clsError;
use classes\clsSession;
use engine\clsSysCommon;

class clsSysAcl
{

    /**
     * Inner variable to hold own object of a class
     * @var object $instance - object of the clsSysAcl
     */
    private static $instance = NULL;

    /**
     * variable of DB class , present DB connect
     * @var Doctrine\ORM\EntityManager $db
     */
    protected $em = "";

    /**
     * variable for error mesage
     * @var $error object of clsSysError
     */
    protected $error = NULL;

    /**
     * variable for error mesage
     * @var $error object of clsSysError
     */
    protected $session = NULL;

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsSysAcl();
        }
        return self::$instance;
    }

    /**
     * Constructor for clsUser class
     *
     */
    public function __construct()
    {
        $this->em = clsDB::getInstance();
        $this->getClsError();
        $this->session = clsSysStorage::getInstance()->initStorage();
    }

    /**
     * Enter description here ...
     */
    protected function getClsError()
    {
        if (clsSysCommon::isProjectOn()) {
            $this->error = clsError::getInstance();
        } else {
            $this->error = clsSysError::getInstance();
        }
    }

    /**
     * Check permission for current user by role name
     *
     * @param string $controller
     * @param string $action
     * @param bool $view_error
     * @param string $role_type
     *
     * @return boolean
     */
    public function CheckPermissions($controller, $action, $view_error = true, $role_type = "guest")
    {
        $return = false;

        if (empty($controller) || empty($action)) {
            $this->error->setError(sprintf('controller_or_action_empty'));
        }

        $perm_name = trim($controller) . ' ' . trim($action);
        $perm_name = strtolower($perm_name);

        $perm_session = $this->session->GetParam('permission', $role_type);

        if (in_array($perm_name, $perm_session)) {
            $return = true;
        } else {
            if ($view_error) {
                $this->error->setError(sprintf('permission_denied'));
            }
        }

        return $return;
    }

    /**
     * Check Admin Permissions for Page action
     * @param string $controller
     * name of the Page(Controller)
     * @param string $action
     * name of the Action
     * @param array $user
     * Administrator data from DB(id, name, role_name, permissions)
     * @return boolean
     */
    public function CheckAdminPermissions($controller, $action, $user){
        $res = false;
        $perm_name = trim($controller) . ' ' . trim($action);
        if (in_array($perm_name, $user['permissions'])) {
            $res = true;
        }
        return $res;
    }

    /**
     * Select all permissions record by role
     *
     * @param string $roleName
     *
     * @return array
     */
    function GetPermissionsByRole($roleName = '')
    {
        $sql = 'SELECT ap.id as perm_id, ap.name as perm_name
					FROM acl_permissions ap
					INNER JOIN acl_permissionsroles apr ON (ap.id=apr.perm_id)
					INNER JOIN acl_roles ar ON (ar.id=apr.role_id AND ar.name = ?)';
        $qb = $this->em->createQueryBuilder();

        $result = $this->em->getAll($sql, array(addslashes($roleName)));
        return $result;
    }

}