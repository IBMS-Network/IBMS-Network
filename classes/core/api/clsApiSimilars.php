<?php
class clsApiSimilars {
	/**
	* Self instance 
	* 
	* @var clsApiSimilars
	*/
	static private $instance = NULL;
			
	/**
	* Api core
	* 
	* @var clsApiCore
	*/
	protected $api;
    
    /**
	* Product similars
	* 
	* @var clsProductSimilars
	*/
	protected $productSimilars;
	
	
	/**
	* Constructor
	* 
	*/
	public function __construct() {
        $this->productSimilars = clsProductSimilars::getInstance();
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
	* @var clsApiSimilars
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiSimilars();
		}
		return self::$instance;
	}
	
	public function parseItems($items, $args) {
		$result = array();
        
        $similars = $this->api->getArrayNode($items['product_id']);
        clsApiParser::$productsSimilars[$args['product_id']] = $similars;        
        
		return $result;
	}
}
