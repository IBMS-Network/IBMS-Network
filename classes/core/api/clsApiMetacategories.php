<?php

class clsApiMetacategories extends clsApiParser {

    /**
     * Self instance 
     * 
     * @var clsApiMetacategories
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
    * Api Categories object
    * 
    * @var clsApiCategories
    */
    protected $apiCategories;
    
    /**
	* Product similars
	* 
	* @var clsProductSimilars
	*/
	protected $productSimilars;
    
    /**
	* Pages Metas
	* 
	* @var clsPagesmetas
	*/
	protected $pagesmetas;

    /**
     * Constructor
     * 
     */
    public function __construct() {
        $this->categorys = clsCategory::getInstance();
        $this->apiCategories = clsApiCategories::getInstance();
        $this->apiSynonyms = clsApiSynonyms::getInstance();
        $this->productSimilars = clsProductSimilars::getInstance();
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
     * @var clsApiMetacategories
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiMetacategories();
        }
        return self::$instance;
    }

    public function parseItems($items, $args) {
        $result = array();
        if(isset($items['metacategory'])) {
            $metacategories = $this->api->getArrayNode($items['metacategory']);
            
            foreach ($metacategories as $item) {
                // update data
                $categoryId = $this->_updateCategory($item, 0);

                if (!empty($categoryId)) {
                    $entityTypeId = self::getEntityTypeIdByName('category');
                    $pageTypeId = self::getPageTypeIdByName('category');
                    
                    clsApiParser::$allCategories[$item['id']] = $categoryId;

                    if(!empty(clsApiParser::$metacategorySynonyms)) {

                        // clear by ids
                        clsSynonyms::getInstance()->clearByCategoryId($entityTypeId, $categoryId);
                        
                        if(isset(clsApiParser::$metacategorySynonyms[$item['id']])) {
                            $this->_updateSynonyms($entityTypeId, $categoryId, clsApiParser::$metacategorySynonyms[$item['id']]);
                            unset(clsApiParser::$metacategorySynonyms[$item['id']]);
                        }
                    }

                    if (!empty(clsApiParser::$categories)) {

                        $args = array('meta_category_id' => $categoryId);
                        $this->apiCategories->setApi($this->api);
                        foreach (clsApiParser::$categories as $value) {
                            $this->apiCategories->parseItems($value, $args);
                        }
                        clsApiParser::$categories = array();
                    }

                    if(!empty(clsApiParser::$productsSimilars)) {
                        foreach(clsApiParser::$productsSimilars as $k => $v) {
                            if (!empty($v) || !is_array($v)) {
                                $this->productSimilars->clearByProductId((int)$k);
                                $this->productSimilars->addSimilars((int)$k, $v);
                            }
                        }
                    }
                    
                    if(!empty(clsApiParser::$metas)) {
                        foreach(clsApiParser::$metas as $value) {
                            foreach($value as $k => $v) {
                                $this->_updateMetas($pageTypeId, clsApiParser::$allCategories[$k], $v);
                            }
                        }
                    }
                }

                // add params args
                $args['meta_category_id'] = (int) $categoryId;
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