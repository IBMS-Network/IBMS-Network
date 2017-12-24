<?php

namespace classes;

use classes\core\clsDB;

class clsProductsMarkdown {

    static private $instance = NULL;
    private $em = NULL;
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsProductsMarkdown();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->em = clsDB::getInstance();
    }
    
    public function getProductsForMain()
    {
        return $this->em->getRepository('entities\ProductsMarkdown')->findBy(array(), array('id' => DESC), 4);
    }
}