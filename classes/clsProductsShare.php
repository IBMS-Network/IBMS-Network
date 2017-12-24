<?php

namespace classes;

use classes\core\clsDB;

class clsProductsShare {

    static private $instance = NULL;
    private $em = NULL;
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsProductsShare();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->em = clsDB::getInstance();
    }
    
    public function getProductsForMain()
    {
        return $this->em->getRepository('entities\ProductsShare')->findBy(array(), array('id' => DESC), 8);
    }
}