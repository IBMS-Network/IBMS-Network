<?php
class clsApiSynonyms {
	/**
	* Self instance 
	* 
	* @var clsApiSynonyms
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
	* @var clsApiSynonyms
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiSynonyms();
		}
		return self::$instance;
	}
	
	public function parseItems($items, $args) {
		$result = array();
		if (!empty($args['entity_type_id']) && !empty($args['item_id'])){

            $synonyms = $this->api->getArrayNode($items['synonym']);			
			
			// add for ids
			foreach ($synonyms As $synonym){
				$synonymId = clsSynonyms::getInstance()->addSynonym($args['entity_type_id'], $args['item_id'], $synonym);
				
				if (empty($synonymId)) {
					$this->api->log(3, sprintf('Method: %s, Line: %s => Synonym don\'t added!', __METHOD__, __LINE__));
					continue;
				}
			}
		}
		
		return $result;
	}
}
