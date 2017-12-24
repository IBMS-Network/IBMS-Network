<?php

namespace engine;

class clsMobStorage extends clsSysStorage
{
    
    private static $instance = NULL;
    
    public function __construct()
    {
        $this->initStorage();
    }
    
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsMobStorage();
        }
        return self::$instance;
    }
    
    /**
     * Init storage data engine 
     * 	for use on project
     * 
     * @return Object
     */
    public function initStorage()
    {
        $adapterClassName = '';
        if (constant('MODULE_MOBILE_SESSION_ADAPTER')) {
            $adapterClassName = 'engine\\session\\adapter\\cls' . ucfirst(MODULE_MOBILE_SESSION_ADAPTER) . 'Adapter';
        }
        if (class_exists($adapterClassName)) {
            $this->_storage = new $adapterClassName();
        } else {
            parent::initStorage();
        }
        
        return $this->_storage;
    }
    
}