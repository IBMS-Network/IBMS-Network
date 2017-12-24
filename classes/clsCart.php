<?php

namespace classes;

use classes\core\clsDB;
use classes\clsSession;

class clsCart
{

    /**
     * self object
     * @var clsAdminRoles
     */
    private static $instance = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $session = "";
    
    /**
     * Singleton
     * @return NULL|\classes\clsCart
     */
    public static function getInstance()
    {
        if( self::$instance == NULL ){
            self::$instance = new clsCart();
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
        $this->session = clsSession::getInstance();
    }

    public function addToCart($products = array())
    {
        if (!empty($products) && is_array($products)) {
            return $this->session->addToCartSession($products);
        } else {
            return array('result' => false, 'error' => 'Нет данных!');
        }
    }

}