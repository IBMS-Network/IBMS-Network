<?php

namespace classes;

use classes\core\clsDB;
use classes\core\clsCommon;
use engine\clsSysCommon;

/**
 * clsUser class perform methods and action for project Users
 */
class clsUser {
    const STATUS_USER_ALREADY_EXISTS = -1;
    const STATUS_USER_DATA_INVALID = -2;
    /**
     * Inner variable to hold own object of a class
     *
     * @var object $instance - object of the clsUser
     */
    private static $instance = NULL;
    
    /**
     * variable of DB class , present DB connect
     *
     * @var $db object
     */
    private $db = "";
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em = null;
    private $partnerID = "";
    
    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsUser();
        }
        
        return self::$instance;
    }
    
    /**
     * Constructor for clsUser class
     *
     */
    public function __construct() {
        $this->em = clsDB::getInstance();
    }
    
    /**
     * Create user item
     *
     * @todo To do refactoring. Why so many params?! Make by one array as params
     *
     * @param string $name
     * @param string $surname
     * @param string $phone
     * @param string $email
     * @param string $pass
     * @param string $address
     * @param string $fullName
     *
     * @return integer
     */
    public function createUser($name, $surname, $email, $pass, $regDate = '', $status = 1) {
        $return = 0;
        if ($name && $surname && $email && $pass) {

            $user = $this->em->getRepository('entities\User')->findOneBy(array('email' => $email));
            
            if ($user) {
                $return = static::STATUS_USER_ALREADY_EXISTS;
            } else {
                $user = new \entities\User();
                $user->setFirstName($name);
                $user->setLastName($surname);
                $user->setEmail($email);
                $user->setPassword($pass);
                $user->setRegDate($regDate);
                $user->setStatus($status);
                $this->em->persist($user);
                $this->em->flush();

                $return = $user->getId();
            }
            
        } else {
            $return = static::STATUS_USER_DATA_INVALID;
        }
        return $return;
    }
    
    /**
     * Get user data
     *    by user access token
     *
     * @param string $token
     *
     * @return array
     */
    public function getUserInfoByToken($token)
    {
        $user = $this->em->getRepository('entities\User')->findOneBy(array('token' => $token));
        
        if ($user ) {
            $user = $user->getArrayCopy();
            if (isset($user['password'])) {
                unset($user['password']);
            }
        }

        return $user;
    }
    
    /**
     * Get user data by e-mail & password
     *
     * @param string $email
     * user e-mail
     * @param string $password
     * user password
     *
     * @return array|false
     */
    public function getUserInfo($email, $password) {
        $user = $this->em->getRepository('entities\User')->findOneBy(array('email' => $email, 'password' => md5($password)))->getArrayCopy();
        
        if ($user && isset($user['password'])) {
            unset($user['password']);
        }
        
        return $user;
    }
    
    /**
     * Get user data by id
     *
     * @param integer $id
     *
     * @return array
     */
    public function getUserById($id) {
        $user = $this->em->getRepository('entities\User')->find((int)$id)->getArrayCopy();
        
        return $user;
    }
    
    /**
     * Get user data by email
     *
     * @param string $email
     *
     * @return User
     */
    public function getUserByEmail($email) {
        $user = $this->em->getRepository('entities\User')->findOneByEmail($email);
        
        return $user;
    }
    
    /**
     * Update user data
     * 
     * @param array $data
     * @param int $editorIsAdmin
     * @return entities\User
     */
    public function updateUser($data = array(), $editorIsAdmin = 0) {
        $return = false;
        if (!empty($data) && is_array($data)){
            $user_id = (int)$data['id'];
            unset($data['id']);
            
            $user = $this->em->getRepository('entities\User')->find($user_id);
            if ($user && !empty($data)){
                if (isset($data['first_name'])) {
                    $user->setFirstName($data['first_name']);
                }
                if (isset($data['last_name'])) {
                    $user->setLastName($data['last_name']);
                }
                if (isset($data['email'])) {
                    $user->setEmail($data['email']);
                }
                if (isset($data['password']) && !empty($data['password'])) {
                    $user->setPassword($data['password']);
                }
                if (isset($data['phone'])) {
                    $user->setPhone($data['phone']);
                }
                if (isset($data['city'])) {
                    $user->setCity($data['city']);
                }
                if (isset($data['sex'])) {
                    $user->setSex($data['sex']);
                }
                if (isset($data['birth_date'])) {
                    $user->setBirthDate($data['birth_date']);
                }
                $this->em->persist($user);
                $this->em->flush();
                
                $return = $user;
            }
            
//            if (!$editorIsAdmin) {
//                $session = clsSession::getInstance();
//                $session->setUserSessionByUserId($user_id);
//            }
            
        }
        return $return;
    }
    
    public function getUserCostById($id) {
        
        $sql = "SELECT uc.name FROM users_costs uc
				JOIN users u ON uc.id = u.users_costs_id
				WHERE u.id = ? LIMIT 1";
        $sqlArr = array($id);
        $res = $this->db->getRow($sql, $sqlArr);
        
        if (isset($res['name'])) {
            return (int)$res['name'];
        }
        
        return 0;
    }
    
    public function getPasswordById($id) {
        
        $sql = "SELECT password FROM users WHERE id = ? LIMIT 1";
        $sqlArr = array($id);
        $res = $this->db->getRow($sql, $sqlArr);
        
        if (isset($res['password'])) {
            return $res['password'];
        }
        
        return false;
    }
    
    public function isUniqueEmail($email) {
        if (is_string($email) && !empty($email)) {
            if(!$this->em->getRepository('entities\User')->findBy(array('email' => $email))) {
                return true;
            }
        }
        
        return false;
    }

    public function getAll() {
        $sql = "SELECT * FROM users  WHERE 1";
        
        return $this->db->getAll($sql);
    }
    
    /**
     * Get list data
     *
     * @param integer $count
     * count items in list
     * @param integer $offset
     * offset start position in list
     *
     * @return array
     */
    public function getList($count = 10, $offset = 0) {
        $count = clsSysCommon::isInt($count);
        $offset = clsSysCommon::isInt($offset);
        
        $return = array();
        
        if (!empty($count)) {
            $return = $this->em->getRepository('entities\User')->findBy(array(), array(), $count, $offset);
            
            array_walk($return, create_function('&$val', '$val = $val->getArrayCopy();'));
        }
        
        return $return;
    }
    
    public function isActiveUser($id) {
        $result = false;
        
        if (!empty($id) && is_int($id)) {
            $sql = "SELECT COUNT(*) FROM users u
                   WHERE u.id = ? AND u.password != ''";
            
            $sqlArr = array($id);
            $res = $this->db->getRow($sql, $sqlArr);
            
            if ($res) {
                $result = true;
            }
        }
        
        return $result;
    }
    
    public function activateByEmail($email) {
        $result = false;
        if (!empty($email) && is_string($email)) {
            $sql = "UPDATE users SET status = 1 WHERE email = ?";
            $res = $this->db->Execute($sql, $email);
            if ($res) {
                $result = true;
            }
        }
        
        return $result;
    }
    
    /**
     * Delete user item in DB
     *
     * @param integer $user_id
     *
     * @return boolean
     */
    public function deleteUser($user_id) {
        $result = false;
        if (clsCommon::isInt($user_id)) {
            $user = $this->em->getRepository('entities\User')->find((int)$user_id);
            if ($user){
                $this->em->remove($user);
                $this->em->flush();
                $result = true;
            }
        }
        return (bool)$result;
    }
    
    /**
     * Set token for exists user by id
     *
     * @param integer $user_id
     * @param string $token
     *
     * @return boolean
     */
    public function setToken($user_id, $token = '') {
        $result = false;
        if (clsCommon::isInt($user_id)) {
            $user = $this->em->getRepository('entities\User')->find((int)$user_id);
            
            $user->setToken($token);
            $this->em->persist($user);
            $this->em->flush();
            
            $result = (bool)$user->getId();
        }
        
        return $result;
    }
    
    /**
     * Get token value for exists user by id
     *
     * @param integer $user_id
     *
     * @return string
     */
    public function getToken($user_id) {
        $result = '';
        if (clsCommon::isInt($user_id)) {
            $user = $this->em->getRepository('entities\User')->find((int)$user_id);
            if ($user){
                $result = $user->getToken();
            }
        }
        
        return $result;
    }

}