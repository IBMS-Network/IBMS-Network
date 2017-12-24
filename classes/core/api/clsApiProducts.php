<?php
class clsApiProducts extends clsApiParser{
    /**
    * Self instance 
    * 
    * @var clsApiProducts
    */
    static private $instance = NULL;

    /**
    * Api core
    * 
    * @var clsApiCore
    */
    protected $api;

    /**
    * Products object
    * 
    * @var clsProducts
    */
    protected $products;

    /**
    * Price products
    * 
    * @var clsProductPrices
    */
    protected $productPrices;
    
    /**
    * Products Groups Products
    * 
    * @var clsProductGroups
    */
    protected $productsGroupsProducts;

    /**
    * Quantities object
    * 
    * @var clsQuantities
    */
    protected $quantities;

    /**
    * Deliveries object
    * 
    * @var clsDeliveries
    */
    protected $deliveries;

    /**
    * Product Attribute Value object
    * 
    * @var clsProductAttributeValue
    */
    protected $productAttributeValue;

    /**
    * Api Attributes object
    * 
    * @var clsApiAttributes
    */
    protected $apiAttributes;
    
    /**
    * Api Synonyms object
    * 
    * @var clsApiSynonyms
    */
    protected $apiSynonyms;

    /**
    * Attributes Values object
    * 
    * @var clsAttributesValues
    */
    protected $attributesValues;

    /**
    * Category Attributes object
    * 
    * @var clsCategoryAttributes
    */
    protected $categoryAttributes;

    /**
    * Constructor
    * 
    */
    public function __construct() {
        $this->products = clsProducts::getInstance();
        $this->productPrices = clsProductPrices::getInstance();
        $this->productsGroupsProducts = clsProductsGroups::getInstance();
        $this->quantities = clsQuantities::getInstance();
        $this->deliveries = clsDeliveries::getInstance();
        $this->productAttributeValue = clsProductAttributeValue::getInstance();
        $this->apiAttributes = clsApiAttributes::getInstance();
        $this->attributesValues = clsAttributesValues::getInstance();
        $this->categoryAttributes = clsCategoryAttributes::getInstance();
        $this->apiSynonyms = clsApiSynonyms::getInstance();
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
    * @var clsApiProducts
    */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiProducts();
        }
        return self::$instance;
    } 

    public function parseItems($items, $args) {
        $result = array();
        if(isset($items['product'])) {
            $products = $this->api->getArrayNode($items['product']);

            if (isset($args['from']) && $args['from'] == 'order') {
                if(isset($items['product'][0])) {
                    $orderId = (int)$args['item_id'];
                    $orderProducts = clsOrderProducts::getInstance();
                    $orderProducts->clearProductsByOrderId($orderId);

                    foreach($products as $product) {
                        $newResultItem = &$result['product'][];
                        $productId = $this->products->getIdByOuterId((int)$product['id']);
                        $quantity= $product['number'];
                        $price = $product['price'];
                        $deliveryId = 0;

                        $newResultItem['id'] = $productId;
                        $newResultItem['status'] = empty($productId) ? '0' : '1';
                        if(!empty($productId)) {
                            $orderProducts->addProduct($orderId, $deliveryId, $productId, $quantity, $price);
                        }
                    }                
                }

            } else {

                foreach ($products as $product) {
                    $newResultItem = &$result['product'][];

                    // set quantity
                    $productQuantity = empty($product['quantities']['quantity']['value']) ? 0 : $product['quantities']['quantity']['value'];
                    $productQuantityMin = empty($product['quantities']['quantity']['min']) ? 0 : $product['quantities']['quantity']['min'];

                    $productQuantityId = '';
                    if(!empty($productQuantity)) {
                        $productQuantityId = $this->quantities->getIdByNumber($productQuantity, $productQuantityMin);
                        if (empty($productQuantityId)) {
                            $productQuantityId = $this->quantities->addNumber($productQuantity, $productQuantityMin);
                        }
                    }

                    if (empty($productQuantityId)) {
                        $this->api->log(3, sprintf('Method: %s, Line: %s => Product quantity don\'t added!', __METHOD__, __LINE__));
                        continue;
                    }

                    // set delivery
                    $productDelivery = empty($product['delivery']) ? '' : $product['delivery'];

                    $productDeliverieId = self::getProductDeliveryIdByName($productDelivery);
                    if (empty($productDeliverieId)) {
                        $productDeliverieId = $this->deliveries->addName($productDelivery);
                    }

                    if (empty($productDeliverieId)) {
                        $this->api->log(3, sprintf('Method: %s, Line: %s => Product delivery don\'t added!', __METHOD__, __LINE__));
                        continue;
                    }


                    // get data product
                    $productArticul = isset($product['articul']) ? $product['articul'] : '';

                    // set product
                    $productId = $this->_updateProduct($product, $args['group_product_id'], $productDeliverieId, $productQuantityId);

                    if ((int)$productId > 0){
                        
                        self::$addedProducts[$product['id']] = $productId;
                        //set group
    //                    if(clsApiParser::$firstGroupAdd == true) {
                            //clear all groups
    //                        $this->productsGroupsProducts->clearByProductId($productId);

    //                        clsApiParser::$firstGroupAdd = false;
    //                    }
                        //add group
                        $productGroupId = $this->productsGroupsProducts->addGroup($args['group_product_id'], $productId);

                        if (empty($productGroupId)) {
                            $this->api->log(3, sprintf('Method: %s, Line: %s => Product group don\'t added!', __METHOD__, __LINE__));
                        }

                        $entityTypeId = self::getEntityTypeIdByName('product');

                        if(!empty(clsApiParser::$attributesTmp['product_group'][$args['group_product_id']])) {

                            foreach(clsApiParser::$attributesTmp['product_group'][$args['group_product_id']] as $key => $value) {
                                $args = array('product_id' => (int) $productId);
                                $this->apiAttributes->setApi($this->api);
                                $this->apiAttributes->parseItems($value['attributes'], $args);
                            }
                        }

                        // clear attributes product
                        $this->productAttributeValue->clearByProductId($productId);

                        if(!empty(clsApiParser::$attributesTmp['product'][$product['id']])) {

                            foreach(clsApiParser::$attributesTmp['product'][$product['id']] as $key => $value) {
                                $args = array('product_id' => (int) $productId);
                                $this->apiAttributes->setApi($this->api);
                                $this->apiAttributes->parseItems($value['attributes'], $args);
                            }
                            unset(clsApiParser::$attributesTmp['product'][$product['id']]);

                        }

                        if(!empty(clsApiParser::$productSynonyms)) {

                            // clear by ids
                            clsSynonyms::getInstance()->clearByCategoryId($entityTypeId, $productId);

                                if(isset(clsApiParser::$productSynonyms[$product['id']])) {
                                    $args = array('entity_type_id' => $entityTypeId,
                                                  'item_id' => (int)$productId);
                                    $this->apiSynonyms->setApi($this->api);
                                    foreach(clsApiParser::$productSynonyms[$product['id']] as $v) {
                                        $this->apiSynonyms->parseItems($v, $args);                            
                                    }
                                    unset(clsApiParser::$productSynonyms[$product['id']]);
                                }
                        }

                        if(!empty(clsApiParser::$imagesTmp)) {

                            clsEntityImages::getInstance()->clearByEntityTypeIdItemId($entityTypeId, $productId);                      
                            clsEntityImages::getInstance()->addImages(clsApiParser::$imagesTmp, $entityTypeId, $productId);                      
                            clsApiParser::$imagesTmp = array();
                        }

                        if(!empty(clsApiParser::$quantitiesTmp)) {
                            clsProductsQuantities::getInstance()->clearByProductId($entityTypeId, $productId);                      
                            clsProductsQuantities::getInstance()->addQuantities(clsApiParser::$quantitiesTmp, $productId);                      
                            clsApiParser::$quantitiesTmp = array();
                        }

                        if(!empty(clsApiParser::$videosTmp)) {

                            clsEntityVideos::getInstance()->clearByEntityTypeIdItemId($entityTypeId, $productId);                      
                            clsEntityVideos::getInstance()->addVideos(clsApiParser::$videosTmp, $entityTypeId, $productId);                      
                            clsApiParser::$videosTmp = array();
                        }

                        // add params args
                        $args['product_id'] = (int)$productId;
                        $newResultItem['articul'] = $productArticul;
                        $newResultItem['status'] = empty($productId) ? '0' : '1';

                        // entity
                        $args['item_id'] = (int)$productId;
                        $args['entity_type_id'] = $entityTypeId;
                        $args['page_type_id'] = self::getPageTypeIdByName('product');

                        $result += $this->api->callParser($product, $args);

                        // clear args
                        $args['product_id'] = false;
                        $args['item_id'] = false;
                        $args['entity_type_id'] = false;
                        $args['page_type_id'] = false;
                    }
                }
            }
        }

        return $result;
    }
}