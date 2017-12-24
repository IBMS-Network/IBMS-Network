<?php

namespace classes;

use classes\core\clsDB;

class clsSliders {
    /** @var null|clsSliders $instance  */
    static private $instance = NULL;
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsSliders();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->em = clsDB::getInstance();
    }
    
    public function getAll($limit = NEWS_LAST_BLOCK_LIMIT) {
        return $this->em->getRepository('entities\Slider')->findBy(array(), array('id' => 'ASC'), $limit);
    }
}