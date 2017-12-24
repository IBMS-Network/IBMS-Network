<?php

namespace engine;

use classes\core\clsDB;

class clsSysServices {
    
    /**
     * Inner variable to hold own object of a class
     * @var object $instance - object of the clsSysServices
     */
    private static $instance = NULL;
    
    /**
     * variable of DB class , present DB connect
     * @var $db object
     */
    private $db = "";
    
    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsSysServices();
        }
        return self::$instance;
    }
    
    /**
     * Constructor for clsSysServices class
     *
     */
    public function __construct() {
        $this->db = clsDB::getInstance();
    }
    
    /**
     * Get service id by name
     * 
     * @param string $name
     * 
     * @return integer
     */
    public function getServiceIdByNames($name) {
        $serviceId = (int)$this->db->getRepository('entities\Service')->findOneBy(array('name' => addslashes($name)))->getId();
        
        return $serviceId;
    }
    
    /**
     * Get list all services 
     * 
     * @return array 
     */
    public function getServiceNames() {
        $service_names = array();
        
        $sql = 'SELECT id, name FROM services;';
        $list = $this->db->getAll($sql);
        
        if (!empty($list)) {
            foreach ( $list as $service ) {
                if (!empty($service['name'])) {
                    $service_names[$service['id']] = $service['name'];
                }
            }
        }
        
        return $service_names;
    }

}