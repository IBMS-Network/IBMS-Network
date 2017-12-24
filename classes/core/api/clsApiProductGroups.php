<?php
class clsApiProductGroups extends clsApiParser{
	/**
	* Self instance 
	* 
	* @var clsApiProductGroups
	*/
	static private $instance = NULL;
			
	/**
	* Api core
	* 
	* @var clsApiCore
	*/
	protected $api;
	
	/**
	* Group products
	* 
	* @var clsGroupProducts
	*/
	protected $groupProducts;
    
    /**
	* Api Products
	* 
	* @var clsProducts
	*/
	protected $apiProducts;
    
    	
	/**
	* Constructor
	* 
	*/
	public function __construct() {
		$this->groupProducts = clsGroupProducts::getInstance();
		$this->apiProducts = clsApiProducts::getInstance();
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
	* @var clsApiProductGroups
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiProductGroups();
		}
		return self::$instance;
	} 
	
	public function parseItems($items, $args) {
		$result = array();
		if (!empty($args['category_id'])) {
			$productGroups = $this->api->getArrayNode($items['product_group']);
			foreach ($productGroups as $item) {
//				$args['aProductAttributesOrd'] = array();
				
				// Create or update item
				$productGroupId = $this->_updateProductGroup($item, $args['category_id']);
				
				if ($productGroupId){
                    if(!empty(clsApiParser::$productsTmp)) {
                        if(isset(clsApiParser::$productsTmp[$item['id']])) {
                            $this->apiProducts->setApi($this->api);
                            foreach(clsApiParser::$productsTmp[$item['id']] as $k => $v) {
                                $args = array('group_product_id' => $productGroupId);
                                    $this->apiProducts->parseItems($v, $args);                            
                            }
                            unset(clsApiParser::$productsTmp[$item['id']]);
                        }
                        unset(clsApiParser::$attributesTmp['product_group'][$args['group_product_id']]);
                    }
                    
                    $args['group_product_id'] = $productGroupId;
					
					// set attributes product (special_attributes)
//					$productAttributesOrd = $this->api->getArrayNode($item['attributes']['attribute']);
//					$args['aProductAttributesOrd'] = $productAttributesOrd;
					
					$result[] = $this->api->callParser($item, $args);
                    
					
					$args['group_product_id'] = false;
				}
			}
        }
                
		return $result;
	}
}
