<?php
class clsApiQuantities extends clsApiParser {
	/**
	* Self instance 
	* 
	* @var clsApiQuantities
	*/
	static private $instance = NULL;
			
	/**
	* Api core
	* 
	* @var clsApiCore
	*/
	protected $api;
	
    /**
     * Quantities object
     * 
     * @var clsQuantities
     */
    protected $quantities;
    
	
	/**
	* Constructor
	* 
	*/
	public function __construct() {
        $this->quantities = clsQuantities::getInstance();
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
	* @var clsApiQuantities
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiQuantities();
		}
		return self::$instance;
	} 
	
	public function parseItems($items, $args) {
		$result = array();
        
        $quantity = $this->api->getArrayNode($items['quantity']);

        foreach ($quantity as $item) {
            // update data
            $min = isset($item['min']) ? $item['min'] : 0;
            $quantityId = 0;
            $quantityId = $this->_updateQuantity($item['value']);

            if(!empty($quantityId)) {
                clsApiParser::$quantitiesTmp[] = array('id' => $quantityId, 'min' => $min);
            }
        }

		return $result;
	}
}
