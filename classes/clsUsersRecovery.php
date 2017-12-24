<?php

namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use entities\Address;
use entities\UsersRecovery;

class clsUsersRecovery
{

    /**
     * self object
     * @var clsUsersRecovery
     */
    private static $instance = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsUsersRecovery
     */
    public static function getInstance()
    {
        if( self::$instance == NULL ){
            self::$instance = new clsUsersRecovery();
        }
        return self::$instance;
    }

    /**
     * Constructorof the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        $this->em = clsDB::getInstance();
    }

    
    public function setRecoveryInfo($email, $hash) {
        $result = false;
        
        if (is_string($email) && !empty($email) && is_string($hash) && !empty($hash)) {
            
            $entity = new UsersRecovery();
            $entity->setEmail($email);
            $entity->setHash($hash);
            $this->em->persist($entity);
            $this->em->flush();
            
            $result = true;
        }
        
        return $result;
    }
    
    public function setRecoveryInfoStatus($email, $hash) {
        $result = false;
        
        if (is_string($email) && !empty($email) && is_string($hash) && !empty($hash)) {
            if($entity = $this->em->getRepository('entities\UsersRecovery')->findOneBy(array('email' => $email, 'hash' => $hash))) {
                $entity->setStatus(1);
                $this->em->persist($entity);
                $this->em->flush();
                
                $result = true;
            }
        }
        
        return $result;
    }
    
    public function getRecoveryInfo($hash) {
        if (is_string($hash) && !empty($hash)) {
            return $this->em->getRepository('entities\UsersRecovery')->findOneBy(array('hash' => $hash, 'status' => 0), array('id' => 'DESC'), 1);
        }
        
        return false;
    }
}