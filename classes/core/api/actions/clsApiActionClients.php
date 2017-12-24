<?php

class clsApiActionClients {

    /**
     * Self instance 
     * 
     * @var clsApiActionClients
     */
    static private $instance = NULL;

    /**
     * Api core
     * 
     * @var clsApiCore
     */
    protected $api;

    /**
     * Constructor
     * 
     */
    public function __construct() {
        
    }

    /**
     * Set Api
     * 
     * @param clsApiCore $api
     */
    public function setApi($api) {
        $this->api = $api;
    }

    /**
     * Get instance
     * 
     * @var clsApiActionClients
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiActionClients();
        }
        return self::$instance;
    }

    public function action($items) {
        $result = array();
        return $result;
    }

}