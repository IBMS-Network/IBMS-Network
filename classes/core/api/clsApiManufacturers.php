<?php

class clsApiManufacturers extends clsApiParser {

    /**
     * Self instance 
     * 
     * @var clsApiMetas
     */
    static private $instance = NULL;

    /**
     * Api core
     * 
     * @var clsApiCore
     */
    protected $api;

    /**
     * Manufacturers object
     * 
     * @var clsManufacturers
     */
    protected $manufacturers;

    /**
     * Constructor
     * 
     */
    public function __construct() {
        $this->manufacturers = clsManufacturers::getInstance();
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
     * @var clsApiMetas
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiManufacturers();
        }
        return self::$instance;
    }

    public function parseItems($items, $args = array()) {
        $result = array();
        if (!empty($items)) {
            $this->_updateManufacturer($items);
        }

        return $result;
    }

}
