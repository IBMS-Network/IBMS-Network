<?php
class clsApiOrdinaryAttributes extends clsApiParser {
	/**
	* Self instance 
	* 
	* @var clsApiOrdinaryAttributes
	*/
	static private $instance = NULL;
			
	/**
	* Api core
	* 
	* @var clsApiCore
	*/
	protected $api;
	
	/**
	* Attributes object
	* 
	* @var clsAttributes
	*/
	protected $attributes;
	
	/**
	* Category Attributes object
	* 
	* @var clsCategoryAttributes
	*/
	protected $categoryAttributes;
	
	/**
	* Attributes Values object
	* 
	* @var clsAttributesValues
	*/
	protected $attributesValues;
	
	/**
	* Product Attribute Value object
	* 
	* @var clsProductAttributeValue
	*/
	protected $productAttributeValue;
	
	/**
	* Constructor
	* 
	*/
	public function __construct() {
		$this->attributes = clsAttributes::getInstance();
		$this->categoryAttributes = clsCategoryAttributes::getInstance();
		$this->attributesValues = clsAttributesValues::getInstance();
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
	* @var clsApiOrdinaryAttributes
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiOrdinaryAttributes();
		}
		return self::$instance;
	} 
	
	public function parseItems($items, $args) {
		$result = array();
        
		
		if (!empty($args['category_id']) && (!isset($args['group_product_id']) || empty($args['group_product_id']))) {
			$subCategoriesAttributesOrd = $this->api->getArrayNode($items['attribute']);
//            var_dump($items);
//            var_dump($args);
			
			// parse
			$this->_parseSubCategoriesAttributes($args['category_id'], $subCategoriesAttributesOrd, 1);
		}
		return $result;
	}
}
