<?php
/**
 * Class to work with admin authorization
 */

namespace classes;

use classes\core\clsAuthorisation;
use engine\clsSysStorage;

class clsAdminAuthorisation
{

    /**
     * @var clsAdminAuthorisation
     */
    private static $instance;

    /**
     * @var clsSysStorage
     */
    private $storage = "";

    public function __construct()
    {
        $this->storage = clsSysStorage::getInstance();
    }

    /**
     * get singleton
     * @return clsAdminAuthorisation
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsAdminAuthorisation();
        }

        return self::$instance;
    }

    /**
     * Method to get storage instance
     * @return clsSysStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Authorisation in admin panel
     *
     * @param string $login administrator login
     * @param string $password administrator password.
     * @return mixed
     */
    public function login($login, $password)
    {
        $admin = clsAdmin::getInstance()->getAdminDataByAuth($login, $password);
        if (!empty($admin)) {
            $this->setAdminSession($admin);
            header('Location: /admin/');
        } else {
            return false;
        }
    }

    public function logout()
    {
        $this->destroyAdminSession();
    }

    /**
     * Check if user is admin
     *
     * @return boolean
     */
    public function isAuthorized()
    {
        $admin_id = $this->storage->getParam('id', 'admin_user');
        if (empty($admin_id)) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getAdminSession()
    {
        return $this->storage->getParam('admin_user');
    }

    /**
     * @param \entities\Admin $admin
     * @return bool
     */
    private function setAdminSession(\entities\Admin $admin)
    {
        $_sess = array(
            'id' => $admin->getId(),
            'admin_name' => $admin->getLogin(),
            'role_name' => $admin->getRole()->getName(),
            'permissions' => $admin->getRole()->getPermissionsInArray()
        );

        $this->storage->setParams($_sess, 'admin_user');
    }

    private function destroyAdminSession()
    {
        $this->storage->clearParams('admin_user');
    }
}