<?php
class clsApiImages extends clsApiParser {
	/**
	* Self instance 
	* 
	* @var clsApiImages
	*/
	static private $instance = NULL;
			
	/**
	* Api core
	* 
	* @var clsApiCore
	*/
	protected $api;
    
    /**
     * Images object
     * 
     * @var clsImages
     */
    protected $images;
	
	
	/**
	* Constructor
	* 
	*/
	public function __construct() {
        $this->images = clsImages::getInstance();
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
	* @var clsApiImages
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiImages();
		}
		return self::$instance;
	}
	
	public function parseItems($items, $args) {
		$result = array();
        
		if (isset($args['entity_type_id']) && !empty($args['entity_type_id'])
                && isset($args['item_id']) && !empty($args['item_id'])) {
            
            $images = $this->api->getArrayNode($items['image']);
            
            foreach ($images as $item) {
                // update data
                $imageId = $this->_updateImage($item, (int)$args['entity_type_id'], (int)$args['item_id']);
                if(!empty($imageId)) {
                    clsApiParser::$imagesTmp[] = $imageId;
                }
            }

		}
		
		return $result;
	}
}
