<?php

class clsApiAttributes extends clsApiParser {

    /**
     * Self instance 
     * 
     * @var clsApiAttributes
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
        $this->productAttributeValue = clsProductAttributeValue::getInstance();
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
     * @var clsApiAttributes
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiAttributes();
        }
        return self::$instance;
    }

    public function parseItems($items, $args) {
        $result = true;

        if (!empty($args['category_id']) && (!isset($args['group_product_id']) || empty($args['group_product_id']))) {
            $subCategoriesAttributesOrd = $this->api->getArrayNode($items['attribute']);

            // parse
            $this->_parseSubCategoriesAttributes($args['category_id'], $subCategoriesAttributesOrd, 1);
        } elseif (!empty($args['product_id'])) {
            $productAttributes = $this->api->getArrayNode($items['attribute']);
            //parse
            $this->_updateProductAttributesValues($args['product_id'], $productAttributes, 1);
        } else {
//            var_dump($args);
        }

        return $result;
    }

}