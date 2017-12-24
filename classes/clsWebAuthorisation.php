<?php
/**
 * Class to work with web authorization
 */

namespace classes;

use classes\core\clsDB;
use engine\clsSysStorage;

class clsWebAuthorisation
{
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

    public function __construct()
    {
        $this->storage = clsSysStorage::getInstance();
        $this->em = clsDB::getInstance();
    }

    /**
     * getInstance function create or return already exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsWebAuthorisation();
        }

        return self::$instance;
    }

    /**
     * Client authorisation
     *
     * @param string $email
     * @param string $password
     * @param string $rememberMe
     * @return array
     */
    public function login($email, $password, $rememberMe = null)
    {
        $result = array('result' => false);
        
        $user = $this->em->getRepository('entities\User')->findOneBy(array('email' => $email));
        if ($user) {
            if (password_verify($password, $user->getPassword())) {
                if(!empty($rememberMe)) {
                    $user->generateToken();
                    $this->em->persist($user);
                    $this->em->flush();
                    
                    $result['rememberMe']['value'] = $user->getToken();
                    $result['rememberMe']['date'] = date('r', strtotime('+ 1 week'));
                }
                $this->setUserSession($user);
                $result['result'] = true;
            }
        }
        
        return $result;
    }

    /**
     * Method to user logout
     * @return void
     */
    public function logout()
    {
        $this->storage->clearParams('auth');
    }

    /**
     * Method to get active user session data
     * @return array|bool
     */
    public function getUserSession()
    {
        $result = false;
        
        if(!$result = $this->storage->getParam('user-data', 'auth')) {
            if(!empty($_COOKIE['remember_me'])) {
                $user = $this->em->getRepository('entities\User')->findOneByToken($_COOKIE['remember_me']);
                if($user) {
                    $this->setUserSession($user);
                    $result = $this->storage->getParam('user-data', 'auth');
                }
            }
        }
        
        return $result;
    }

    /**
     * Method-helper to check user session started
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return (bool) $this->getUserSession();
    }

    /**
     * @param \entities\User $user
     * @return bool
     */
    public function setUserSession(\entities\User $user)
    {
        clsSysStorage::getInstance()->setParams(array('user-data' => $user->getArrayCopy()), 'auth');
    }
} 