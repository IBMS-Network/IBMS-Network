<?php

class clsApiSubcategories extends clsApiParser {

    /**
     * Self instance 
     * 
     * @var clsApiSubcategories
     */
    static private $instance = NULL;

    /**
     * Categorys object
     * 
     * @var clsCategory
     */
    protected $categorys;
    
//    /**
//	* Subcategory metas
//	* 
//	* @var array
//	*/
//	protected static $metas = array();

    /**
     * Category Attributes object
     * 
     * @var clsCategoryAttributes
     */
    protected $categoryAttributes;
    
    /**
     * Attributes object
     * 
     * @var clsAttributes
     */
    protected $attributes;
    
    /**
     * Attributes Values object
     * 
     * @var clsAttributesValues
     */
    protected $attributesValues;
    
    /**
    * Api Synonyms object
    * 
    * @var clsApiSynonyms
    */
    protected $apiSynonyms;
    
    /**
	* Api Group products object
	* 
	* @var clsApiProductGroups
	*/
	protected $apiProductGroups;
    
    /**
	* Api Attributes object
	* 
	* @var clsApiAttributes
	*/
	protected $apiAttributes;

    /**
     * Api core
     * 
     * @var clsApiCore
     */
    protected $api;
    
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
        $this->categoryAttributes = clsCategoryAttributes::getInstance();
        $this->attributes = clsAttributes::getInstance();
        $this->attributesValues = clsAttributesValues::getInstance();
        $this->apiSynonyms = clsApiSynonyms::getInstance();
        $this->apiProductGroups = clsApiProductGroups::getInstance();
        $this->apiAttributes = clsApiAttributes::getInstance();
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
     * @var clsApiSubcategories
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiSubcategories();
        }
        return self::$instance;
    }

    public function parseItems($items, $args) {
        $result = array();
        if (isset($items['subcategory'])) {
            $categories = $this->api->getArrayNode($items['subcategory']);

            $_categoryUpdate = false;
            if (!empty($args['category_id']) && (int) $args['category_id'] > 0) {
                $_categoryUpdate = true;
            }

            foreach ($categories as $item) {
                if ($_categoryUpdate) {
                    $subCategoryId = $this->_updateCategory($item, (int) $args['category_id']);
                } else {
                    $subCategoryId = $this->_getCategoryIdByOuterId($item['id']);
                }

                if (!empty($subCategoryId)) {
                    
                    if(!$_categoryUpdate) {
                        
                    }
                    clsApiParser::$allCategories[$item['id']] = $subCategoryId;
                    //clear
                    $this->categoryAttributes->clearByCategoryId($subCategoryId);
                    if(!empty(clsApiParser::$attributesTmp['subcategory'][$item['id']])) {
                        
                        foreach(clsApiParser::$attributesTmp['subcategory'][$item['id']] as $key => $value) {
                            $args = array('category_id' => (int) $subCategoryId);
                            $this->apiAttributes->setApi($this->api);
                            $this->apiAttributes->parseItems($value['attributes'], $args);
                        }
                        unset(clsApiParser::$attributesTmp['subcategory'][$item['id']]);
                        
                    }
                    if(clsApiParser::$attributesTmp['attributes']){
                        $this->_parseSpecialAttributes(clsApiParser::$attributesTmp['attributes']);
                    }
                    if(!empty(clsApiParser::$manufacturersProducts)) {
                        $this->_parseManufacturersProducts(clsApiParser::$manufacturersProducts);
                    }
                                        
                    $entityTypeId = self::getEntityTypeIdByName('category');
                    $pageTypeId = self::getPageTypeIdByName('category');
                    
                    if(!empty(clsApiParser::$subcategorySynonyms)) {
                        
                        // clear by ids
                        clsSynonyms::getInstance()->clearByCategoryId($entityTypeId, $subCategoryId);
//                        
                        if(isset(clsApiParser::$subcategorySynonyms[$item['id']])) {
                            $this->_updateSynonyms($entityTypeId, $subCategoryId, clsApiParser::$subcategorySynonyms[$item['id']]);
                            unset(clsApiParser::$subcategorySynonyms[$item['id']]);
                        }
                    }
                    
                    if(!empty(clsApiParser::$metas)) {
                        foreach(clsApiParser::$metas as $value) {
                            foreach($value as $k => $v) {
                                $this->_updateMetas($pageTypeId, clsApiParser::$allCategories[$k], $v);
                            }
                        }
                    }
      
                    if(!empty(clsApiParser::$productsGroups)) {
                        if(isset(clsApiParser::$productsGroups[$item['id']])) {
                            $this->apiProductGroups->setApi($this->api);
                            foreach(clsApiParser::$productsGroups[$item['id']] as $k => $v) {
                                $args = array('category_id' => $subCategoryId);
                                    $this->apiProductGroups->parseItems($v, $args);                            
                            }
                            unset(clsApiParser::$productsGroups[$item['id']]);
                        }
                    }
                }

                // add params args
                $args['category_id'] = (int) $subCategoryId;
                // entity
                $args['item_id'] = (int) $subCategoryId;
                $args['entity_type_id'] = $entityTypeId;
                $args['page_type_id'] = self::getPageTypeIdByName('category');

                $result += $this->api->callParser($item, $args);

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