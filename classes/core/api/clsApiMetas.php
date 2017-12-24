<?php
class clsApiMetas extends clsApiParser {
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
	* Pages Metas object
	* 
	* @var clsPagesmetas
	*/
	protected $pagesmetas;
	
	/**
	* Constructor
	* 
	*/
	public function __construct() {
		$this->pagesmetas = clsPagesmetas::getInstance();
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
			self::$instance = new clsApiMetas();
		}
		return self::$instance;
	}
	
	public function parseItems($items, $args = array()) {
		$result = array();
		if(!empty($items)) {
            if (isset($args['page_type_id']) && isset($args['item_id']) && !empty($args['page_type_id']) && !empty($args['item_id'])){
                $this->_parseMeta($items, $args['page_type_id'], $args['item_id']);
            }
        }
		
		return $result;
	}
}
