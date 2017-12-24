<?php
class clsApiVideos extends clsApiParser {
	/**
	* Self instance 
	* 
	* @var clsApiVideos
	*/
	static private $instance = NULL;
			
	/**
	* Api core
	* 
	* @var clsApiCore
	*/
	protected $api;
    
    /**
     * Videos object
     * 
     * @var clsVideos
     */
    protected $videos;
    
	/**
	* Constructor
	* 
	*/
	public function __construct() {
        $this->videos = clsVideos::getInstance();
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
	* @var clsApiVideos
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiVideos();
		}
		return self::$instance;
	}
	
	public function parseItems($items, $args) {
		$result = array();
        
		if (isset($args['entity_type_id']) && !empty($args['entity_type_id'])
                && isset($args['item_id']) && !empty($args['item_id'])) {
            
            $videos = $this->api->getArrayNode($items['video']);
            
            foreach ($videos as $item) {
                // update data
                $videoId = $this->_updateVideo($item, (int)$args['entity_type_id'], (int)$args['item_id']);
                if(!empty($videoId)) {
                    clsApiParser::$videosTmp[] = $videoId;
                }
            }

		}
		
		return $result;
	}
}
