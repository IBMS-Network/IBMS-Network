<?php

class clsApiCategories extends clsApiParser {

    /**
     * Self instance 
     * 
     * @var clsApiCategories
     */
    static private $instance = NULL;

    /**
     * Api core
     * 
     * @var clsApiCore
     */
    protected $api;

    /**
     * Categorys object
     * 
     * @var clsCategory
     */
    protected $categorys;
    
    /**
    * Api Synonyms object
    * 
    * @var clsApiSynonyms
    */
    protected $apiSynonyms;
    
    /**
    * Api Subcategories object
    * 
    * @var clsApiSubcategories
    */
    protected $apiSubcategories;
    
//    /**
//	* Category metas
//	* 
//	* @var array
//	*/
//	public static $metas = array();

    /**
     * Constructor
     * 
     */
    public function __construct() {
        $this->categorys = clsCategory::getInstance();
        $this->apiSynonyms = clsApiSynonyms::getInstance();
        $this->apiSubcategories = clsApiSubcategories::getInstance();
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
     * @var clsApiCategories
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiCategories();
        }
        return self::$instance;
    }

    public function parseItems($items, $args) {

        $result = array();

        if (!empty($args['meta_category_id']) && (int) $args['meta_category_id'] > 0 && isset($items['category'])) {
            $categories = $this->api->getArrayNode($items['category']);

            foreach ($categories as $item) {
                // update data
                $categoryId = $this->_updateCategory($item, $args['meta_category_id']);

                if (!empty($categoryId)) {
                    $entityTypeId = self::getEntityTypeIdByName('category');
                    
                    clsApiParser::$allCategories[$item['id']] = $categoryId;
//                    if(!empty(clsApiParser::$metas)) {
//                        foreach(clsApiParser::$metas as $key => $value) {
//                            if(isset($value[$item['id']])) {
//                                $args = array('page_type_id' => $entityTypeId,
//                                              'item_id' => (int) $categoryId);
//                                $metas = clsApiMetas::getInstance();
//                                $metas->setApi($this->api);
//                                $metas->parseItems($value[$item['id']]['category']['metas'], $args);
//                                unset(clsApiParser::$metas[$key]);
//                                break;
//                            }
//                        }
//                    }
                    
                    if(!empty(clsApiParser::$categorySynonyms)) {
                        
                        // clear by ids
                        clsSynonyms::getInstance()->clearByCategoryId($entityTypeId, $categoryId);
                        
                        if(isset(clsApiParser::$categorySynonyms[$item['id']])) {
                            $this->_updateSynonyms($entityTypeId, $categoryId, clsApiParser::$categorySynonyms[$item['id']]);
                            unset(clsApiParser::$categorySynonyms[$item['id']]);
                        }
                    }
                    
//                    if(!empty(clsApiParser::$categorySynonyms)) {
//                        
//
//                        // clear by ids
//                        clsSynonyms::getInstance()->clearByCategoryId($entityTypeId, $categoryId);
//                        
//                        $args = array('entity_type_id' => $entityTypeId,
//                                      'item_id' => (int)$categoryId);
//                        $this->apiSynonyms->setApi($this->api);
//                        foreach(clsApiParser::$categorySynonyms as $value) {
//                            $this->apiSynonyms->parseItems($value, $args);                            
//                        }
//                        clsApiParser::$categorySynonyms = array();
//                    }
                    if(!empty(clsApiParser::$subcategories)) {
                        
                        $args = array('category_id' => $categoryId);
                        $this->apiSubcategories->setApi($this->api);
                        foreach(clsApiParser::$subcategories as $value) {
                            $this->apiSubcategories->parseItems($value, $args);                            
                        }
                        clsApiParser::$subcategories = array();
                    }
                }

                // add params args
                $args['category_id'] = (int) $categoryId;
                // entity
                $args['item_id'] = (int) $categoryId;
                $args['entity_type_id'] = $entityTypeId;
                $args['page_type_id'] = self::getPageTypeIdByName('category');

                $result[] = $this->api->callParser($item, $args);

                // clear args
                $args['category_id'] = false;
                $args['item_id'] = false;
                $args['entity_type_id'] = false;
                $args['page_type_id'] = false;
            }
        }

        return $result;
    }

}