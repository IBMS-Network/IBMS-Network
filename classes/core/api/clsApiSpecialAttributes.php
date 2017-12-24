<?php
class clsApiSpecialAttributes extends clsApiParser{
	/**
	* Self instance 
	* 
	* @var clsApiSpecialAttributes
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
	* @param clsApiCore $oApi
	*/
	public function setApi($oApi) {
		$this->api = $oApi;
	}
	
	/**
	* Get instance
	* 
	* @var clsApiSpecialAttributes
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiSpecialAttributes();
		}
		return self::$instance;
	} 
	
	public function parseItems($items, $args) {
		$result = array();
		if (!empty($args['category_id']) && (!isset($args['group_product_id']) || empty($args['group_product_id']))) {
			$subCategoriesAttributesSpec = $this->api->getArrayNode($items['attribute']);
			
			// parse
			$this->_parseSubCategoriesAttributes($args['category_id'], $subCategoriesAttributesSpec, 0);
		}
		return $result;
	}
}
