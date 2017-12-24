<?php
class clsApiPrices {
	/**
	* Self instance 
	* 
	* @var clsApiPrices
	*/
	static private $instance = NULL;
			
	/**
	* Api core
	* 
	* @var clsApiCore
	*/
	protected $api;
	
	/**
	* Price products
	* 
	* @var clsProductPrices
	*/
	protected $productPrices;
	
	/**
	* Constructor
	* 
	*/
	public function __construct() {
		$this->productPrices = clsProductPrices::getInstance();
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
	* @var clsApiPrices
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiPrices();
		}
		return self::$instance;
	} 
	
	public function parseItems($items, $args) {
		$result = array();
		
		$prices = $this->api->getArrayNode($items['price']);
		
		if (empty($prices) || !is_array($prices) || count($prices) != COUNT_PRICE_COLUMN_NUMBER) {
			$this->api->log(3, sprintf('Method: %s, Line: %s => Product prices incorrect column number!', __METHOD__, __LINE__));
//			continue;
		}
//        var_dump($prices);
        $this->productPrices->clearByProductId($args['product_id']);
//		foreach ($prices As $keyPrice => $valuePrice){
//			// TODO: create check of count price number
//			$columnNumber = 1 + $keyPrice;
//			
////			if (empty($priceId)) {
//				$priceId = $this->productPrices->addPrice($args['product_id'], $columnNumber, $valuePrice);
////			}
//			
//			if (empty($priceId)) {
//				$this->api->log(3, sprintf('Method: %s, Line: %s => Product price don\'t added!', __METHOD__, __LINE__));
//				continue;
//			}
//		}
//        var_dump($args);
        $priceId = $this->productPrices->addPrices($args['product_id'], $prices);
		
		return $result;
	}
}
