<?php

namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use entities\Address;

class clsUsersAddresses
{

    /**
     * self object
     * @var clsUsersAddresses
     */
    private static $instance = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsUsersAddresses
     */
    public static function getInstance()
    {
        if( self::$instance == NULL ){
            self::$instance = new clsUsersAddresses();
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

    /**
     * Get list of user's addresses
     *
     * @return array
     */
    public function getUserAddresses($userId)
    {
        return $this->em->getRepository('entities\Address')->findBy(array('userId' => $userId));
    }
    
    /**
     * Get user address
     *
     * @return array|false
     */
    public function getUserAddress($id)
    {
        return $this->em->getRepository('entities\Address')->findOneById((int)$id);
    }
    
    /**
     * Update user address
     * 
     * @param array $data
     * Address params
     * @return boolean
     */
    public function updateAddress($data = array()) {
        $return = false;
        if (!empty($data) && is_array($data)){
            $address_id = clsCommon::isInt($data['id']);
            unset($data['id']);
            
            if(!empty($address_id)) {
                $address = $this->em->getRepository('entities\Address')->find($address_id);
            } else {
                $address = new Address();
                if(isset($data['user_id'])) {
                    $address->setUserId($data['user_id']);
                    unset($data['user_id']);
                }
            }
            if ($address && !empty($data)){
                if (isset($data['address'])) {
                    $address->setAddress($data['address']);
                }
                $this->em->persist($address);
                $this->em->flush();

                $return = true;
            }
        }
        
        return $return;
    }
    
    /**
     * Delete user address
     * 
     * @param int $id
     * ID of the Address
     * @param int $userId
     * ID of the User
     * @return boolean
     */
    public function deleteUserAddress($id, $userId)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        $userId = clsCommon::isInt($userId);
        if ( $id > 0 && $userId > 0) {
            $address = $this->em->getRepository('entities\Address')->findOneBy(array('id' =>$id, 'userId' => $userId));
            if(!empty($address)) {
                $this->em->remove($address);
                $this->em->flush();
                
                $res = true;
            }
        }
        return $res;
    }
}