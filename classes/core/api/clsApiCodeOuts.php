<?php
class clsApiCodeOuts {
	/**
	* Self instance 
	* 
	* @var clsApiCodeOuts
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
	* @var clsApiCodeOuts
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiCodeOuts();
		}
		return self::$instance;
	} 
	
	public function parseItems($items, $args) {
		$result = array();
		
		if (isset($args['product_id']) && !empty($args['product_id'])){
			$codeouts = $this->api->getArrayNode($items['code_out']);
			
			// clear by product_id
			clsCodes::getInstance()->clearByProductId($args['product_id']);
			
			foreach ($codeouts As $codeout){
				
				$codeoutId = clsCodes::getInstance()->addCodeout($args['product_id'], $codeout);
				
				if (empty($codeoutId)) {
					$this->api->log(3, sprintf('Method: %s, Line: %s => Product code_out don\'t added!', __METHOD__, __LINE__));
					continue;
				}
				
			}
		}
		
		return $result;
	}
}
