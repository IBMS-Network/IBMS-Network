<?php
/**
 * Class to work with web authorization
 */

namespace classes;

use classes\core\clsDB;
use engine\clsSysStorage;

class clsMobAuthorisation {
    /**
     * @var clsWebAuthorisation
     */
    private static $instance = null;
    /**
     * @var \engine\clsSysStorage
     */
    private $storage = null;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em = null;

    public function __construct() {
        $this->storage = clsSysStorage::getInstance();
        $this->em = clsDB::getInstance();
    }

    public function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsMobAuthorisation();
        }

        return self::$instance;
    }

    /**
     * Client authorisation on project by login and password
     *
     * @param string $email
     * @param string $password
     * @return array
     * array('result' => array, 'errors' => array)
     */
    public function login($email, $password) {
        $return = array('result' => array(), 'errors' => array());

        $validEmail = clsValidation::emailValidation($email);
        if ($validEmail) {
            $user = clsMobUser::getInstance()->getUserInfo($validEmail, $password);
            if (!empty($user)) {
                $_user = array();

                // set new token
                $token = $this->_generateToken($user);
                clsMobUser::getInstance()->setToken($user->getId(), $token);
                $_user['id'] = $user->getId();
                $_user['id'] = $user->getEmail();
                $_user['token'] = $token;
                $_user['role_name'] = $user->getRole()->getName();
                $_user['permissions'] = $user->getRole()->getPermissionsInArray();
                $this->_setUserSession($_user, $token);
                $return = array('result' => $_user);
            } else {
                $return = array('errors' => array("user_not_exists"));
            }
        } else {
            $return = array('errors' => array("email_no_valid"));
        }
        return $return;
    }

    /**
     * Login user on project
     * by user id and access token
     *
     * @param string $token
     *
     * @return array
     * array('result' => array, 'errors' => array)
     */
    public function loginByToken($token) {
        $return = array('result' => array(), 'errors' => array());

        // check and clear data
        $token = clsValidation::accessTokenValidation($token);
        if ($token) {
            if($this->storage->isParamSet('authuser' . $token)) {
                $_user = $this->storage->getParam('authuser' . $token);
            }else{
                $user = clsMobUser::getInstance()->getUserInfoByToken($token);
                if (!empty($user)) {
                    $_user['id'] = $user->getId();
                    $_user['id'] = $user->getEmail();
                    $_user['token'] = $token;
                    $_user['role_name'] = $user->getRole()->getName();
                    $_user['permissions'] = $user->getRole()->getPermissionsInArray();
                }
            }
            if($_user) {
                $this->_setUserSession($_user, $token);
                $return = $_user;
            }else {
                $return = array('errors' => array("user_not_exists"));
            }

        } else {
            $return = array('errors' => array("access_data_no_valid"));
        }

        return $return;
    }

    /**
     * Method to user logout
     * @return void
     */
    public function logout() {
        $this->storage->clearParams('auth');
    }

    /**
     * Method to get active user session data
     * @return array|bool
     */
    public function getUserSession() {
        return $this->storage->getParam('user-data', 'auth');
    }

    /**
     * Method-helper to check user session started
     *
     * @return bool
     */
    public function isAuthorized() {
        return (bool)$this->getUserSession();
    }

    /**
     * @param \entities\User $user
     * @param string $token
     */
    private function _setUserSession($user, $token) {
        $this->storage->setParams($user, 'authuser' . $token);
    }

    /**
     * Generate new unique token
     *
     * @param array $user
     *
     * @return string
     * hash token or empty string
     */
    private function _generateToken($user) {
        $token = '';
        if (!empty($user)) {
            $token = sha1(PASS . time() . $user->getId() . rand(1000, 9999));
        }
        return $token;
    }
}