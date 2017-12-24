<?php

class clsApiParser {

    private static $entity_types = array();
    private static $page_types = array();
    private static $meta_types = array();
    private static $product_delivery = array();
    public static $userCompanies = array();
    public static $userAddresses = array();
    public static $synonyms;
    public static $productSynonyms;
    public static $subcategorySynonyms;
    public static $categorySynonyms;
    public static $metacategorySynonyms;
    public static $manufacturersProducts = array();
    public static $categories = array();
    public static $subcategories = array();
    public static $productsGroups = array();
    public static $productsTmp = array();
    public static $productsSimilars = array();
    public static $imagesTmp = array();
    public static $videosTmp = array();
    public static $quantitiesTmp = array();
    public static $attributesTmp = array();
    public static $firstGroupAdd = true;
    public static $allCategories = array();
    public static $addedProducts = array();
    public static $addedManufacturers = array();
    

    /**
     * Subcategory metas
     * 
     * @var array
     */
    public static $metas = array();

    /**
     * Parsing attributes in categories
     * 
     * @param integer $subCategoryId
     * @param array $subCategoriesAttributes
     * @param integer $ordinary
     * 
     * @return bool
     */
    protected function _parseSubCategoriesAttributes($subCategoryId, $subCategoriesAttributes = array(), $ordinary = 0) {
        if (!empty($subCategoriesAttributes)) {

            if (is_array($subCategoriesAttributes) && count($subCategoriesAttributes) > 0) {
                foreach ($subCategoriesAttributes as $attribute) {

                    if (empty($attribute['name']) || empty($attribute['values'])) {
                        continue;
                    }

                    // name
                    $attributeName = $attribute['name'];
                    $attributeHint = empty($attribute['hint']) ? '' : $attribute['hint'];
                    $attributeStep = empty($attribute['step']) ? 0 : $attribute['step'];
                    $attributeOuterId = $attribute['id'];

                    $attributeId = $this->attributes->getIdByName($attributeName);

                    if (empty($attributeId)) {
                        $attributeId = $this->attributes->addAttribute($attributeName, $attributeHint, $attributeOuterId, $attributeStep);
                    } else {
                        $this->attributes->editAttribute($attributeId, $attributeName, $attributeHint, $attributeOuterId);
                    }

                    if (empty($attributeId)) {
                        $this->api->log(3, sprintf('Method: %s, Line: %s => Attribute don\'t added!', __METHOD__, __LINE__));
                        continue;
                    }

                    // join category2attributes
                    $categoryAttributeId = $this->categoryAttributes->getIdByParams($subCategoryId, $attributeId, $ordinary);
                    if (empty($categoryAttributeId)) {
                        $categoryAttributeId = $this->categoryAttributes->addAttribute($subCategoryId, $attributeId, $ordinary);
                    }

                    if (empty($categoryAttributeId)) {
                        $this->api->log(3, sprintf('Method: %s, Line: %s => Category attribute don\'t added', __METHOD__, __LINE__));
                        continue;
                    }

                    // values
                    $attributeValues = $this->api->getArrayNode($attribute['values']);
                    if(isset($attributeValues[0])) {
                        $attributeValues = $attributeValues[0];
                    }
                    
                    if (empty($attributeValues)) {
                        $this->api->log(3, sprintf('Method: %s, Line: %s => Empty required fields: "values"', __METHOD__, __LINE__));
                        continue;
                    }

                    if (isset($attributeValues['value'])) {
                        
                        if (is_array($attributeValues['value'])) {
                            foreach ($attributeValues['value'] As $value) {
                                // fix for hex
                                $value = (is_array($value) && isset($value['name'])) ? $value['name'] : $value;
                                $additional = (is_array($value) && isset($value['hex'])) ? $value['hex'] : 0;
                                $attributeValueId = $this->_updateAttributesValues($attributeId, $value, 0, $additional);
                            }
                        } else {
                            $value = $attributeValues['value'];
                            $value = (is_array($value) && isset($value['name'])) ? $value['name'] : $value;
                            $additional = (is_array($value) && isset($value['hex'])) ? $value['hex'] : 0;
                            $attributeValueId = $this->_updateAttributesValues($attributeId, $value, 0, $additional);
                        }
                    } elseif (isset($attributeValues['from']) && isset($attributeValues['to'])) {

                        // set step
                        if (isset($attributeValues['step'])) {
                            $this->attributes->editAttributeStep($attributeId, $attributeValues['step']);
                        }

                        // from
                        $value = $attributeValues['from'];
                        $attributeValueId = $this->_updateAttributesValues($attributeId, $value, 0);

                        // to
                        $value = $attributeValues['to'];
                        $attributeValueId = $this->_updateAttributesValues($attributeId, $value, 0);

                        // set metka about range
                        $this->categoryAttributes->setIsSelect($categoryAttributeId);
                    } else {
                        $this->api->log(3, sprintf('Method: %s, Line: %s => Attribute values don\'t added!', __METHOD__, __LINE__), $attributeValues);
                        continue;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Parsing special attributes
     * 
     * @param integer $subCategoryId
     * @param array $subCategoriesAttributes
     * @param integer $ordinary
     * 
     * @return bool
     */
    protected function _parseSpecialAttributes($attributes = array()) {
        if (!empty($attributes)) {

            if (is_array($attributes) && count($attributes) > 0) {
                foreach ($attributes as $attribute) {

                    if (empty($attribute['name']) || empty($attribute['values'])) {
                        continue;
                    }

                    // name
                    $attributeName = $attribute['name'];
                    $attributeHint = empty($attribute['hint']) ? '' : $attribute['hint'];
                    $attributeStep = empty($attribute['step']) ? 0 : $attribute['step'];
                    $attributeOuterId = $attribute['id'];

                    $attributeId = $this->attributes->getIdByName($attributeName);

                    if (empty($attributeId)) {
                        $attributeId = $this->attributes->addAttribute($attributeName, $attributeHint, $attributeOuterId, $attributeStep);
                    } else {
//						$this->attributes->editAttribute($attributeId, $attributeName, $attributeHint, $attributeOuterId);
                    }

                    if (empty($attributeId)) {
                        $this->api->log(3, sprintf('Method: %s, Line: %s => Attribute don\'t added!', __METHOD__, __LINE__));
                        continue;
                    }

                    // values
                    $attributeValues = $attribute['values']['value'];

                    if (empty($attributeValues)) {
                        $this->api->log(3, sprintf('Method: %s, Line: %s => Empty required fields: "values"', __METHOD__, __LINE__));
                        continue;
                    }

                    $attributeValuesFromDB = $this->attributesValues->getValuesByAttributeId($attributeId);

                    if (!empty($attributeValuesFromDB)) {
                        foreach ($attributeValuesFromDB as $value) {
                            if (in_array($value['value'], $attributeValues)) {
                                unset($attributeValues[array_search($value['value'], $attributeValues)]);
                            }
                        }
                    }

                    if (!empty($attributeValues)) {
                        foreach ($attributeValues As $value) {
                            $attributeValueId = $this->_insertAttributesValues($attributeId, $value);
                            if (empty($attributeValueId)) {
                                $this->api->log(3, sprintf('Method: %s, Line: %s => Attribute values don\'t added!', __METHOD__, __LINE__));
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Insert Atributes Values
     *   for Special Atributes
     * 
     * @param integer $attributeId
     * @param mixed $value
     * @param integer $secondary
     * 
     * @return integer
     */
    protected function _insertAttributesValues($attributeId, $value = false, $secondary = 0, $additional = 0) {
        if (empty($attributeValueId)) {
            $attributeValueId = $this->attributesValues->addAttribute($attributeId, $value, $secondary, $additional);
        }

        if (empty($attributeValueId)) {
            $this->api->log(3, sprintf('Method: %s, Line: %s => Attribute values don\'t added!', __METHOD__, __LINE__));
            return array();
        }

        return $attributeValueId;
    }

    /**
     * Update Atributes Values
     *   for SubCategories
     * 
     * @param integer $attributeId
     * @param mixed $value
     * @param integer $secondary
     * 
     * @return integer
     */
    protected function _updateAttributesValues($attributeId, $value = false, $secondary = 0, $additional = 0) {
        $attributeValueId = $this->attributesValues->getIdByAttributeIdValue($attributeId, $value, $secondary);
        if (empty($attributeValueId)) {
            $attributeValueId = $this->attributesValues->addAttribute($attributeId, $value, $secondary, $additional);
        } else {
            $this->attributesValues->editAttribute($attributeValueId, $attributeId, $value, $secondary, $additional);
        }

        if (empty($attributeValueId)) {
            $this->api->log(3, sprintf('Method: %s, Line: %s => Attribute values don\'t added!', __METHOD__, __LINE__));
            return array();
        }

        return $attributeValueId;
    }

    /**
     * Update Product Attributes Values
     * 
     * @param integer $productId
     * @param array $productAttributes
     * 
     * @return bool
     */
    protected function _updateProductAttributesValues($productId, $productAttributes) {
        if (!empty($productAttributes) && is_array($productAttributes) && count($productAttributes) > 0) {

            $attributeValuesArray = array();

            foreach ($productAttributes as $attribute) {
                if (empty($attribute['name']) || empty($attribute['values']['value'])) {
                    continue;
                }

                $attributeName = $attribute['name'];
                $attributeValue = $this->api->getArrayNode($attribute['values']['value']);

                $attributeId = $this->attributes->getIdByName($attributeName);
                if (empty($attributeId)) {
                    $this->api->log(3, sprintf('Method: %s, Line: %s => Attribute don\'t exists!', __METHOD__, __LINE__));
                    continue;
                }

                // check on is select
                $cat_atr_Id = $this->categoryAttributes->isSelectByAtributeId($attributeId);

                if ($cat_atr_Id && !empty($cat_atr_Id)) {
                    $secondaryTmp = 1;
                } else {
                    $secondaryTmp = 0;
                }

                foreach ($attributeValue as $value) {
                    $attributeValueId = $this->_updateAttributesValues($attributeId, $value, $secondaryTmp);
                    $attributeValuesArray[] = $attributeValueId;
                }
            }

            $productAttributeValueResult = $this->productAttributeValue->apiAddAttributes($productId, $attributeValuesArray);
            if (empty($productAttributeValueResult)) {
                $this->api->log(3, sprintf('Method: %s, Line: %s => Product Attribute Values Ids don\'t added!', __METHOD__, __LINE__));
            }
        }

        return true;
    }

    /**
     * Update category item
     * 
     * @param array $categories
     * @param integer $parentId
     * 
     * @return integer
     */
    protected function _updateCategory($item, $parentId = 0) {
        $categoryId = false;

        if (isset($item) && !empty($item) && is_array($item)) {
            $categoryName = $item['name'];
            $categoryOuterId = $item['id'];
            $categoryDescription = empty($item['description']) ? '' : $item['description'];
            $categorySecondary = empty($item['secondary']) ? 0 : (int) $item['secondary'];
            $categoryWeight = empty($item['sort_order']) ? 0 : (int) $item['sort_order'];
            $categoryIcon = empty($item['icon']) ? '' : $item['icon'];
            $categoryImageHeight = empty($item['height']) ? 0 : $item['height'];
            $categoryImageWidth = empty($item['width']) ? 0 : $item['width'];
            $categoryImageOffsetX = empty($item['offsetX']) ? 0 : $item['offsetX'];
            $categoryImageOffsetY = empty($item['offsetY']) ? 0 : $item['offsetY'];

            $categoryId = $this->categorys->getCategoryIdByOuterId($categoryOuterId);

            if (empty($categoryId)) {
                $categoryId = $this->categorys->addCategory($parentId, $categoryOuterId, $categoryName, $categoryDescription, $categoryWeight, $categoryIcon,
                        $categorySecondary, $categoryImageHeight, $categoryImageWidth, $categoryImageOffsetX, $categoryImageOffsetY);

                if (empty($categoryId)) {
                    $this->api->log(3, sprintf('Method: %s, Line: %s => Category don\'t added!', __METHOD__, __LINE__));
//					continue;
                }
            } else {
                $this->categorys->editCategory($categoryId, $parentId, $categoryOuterId, $categoryName, $categoryDescription, $categoryWeight, $categoryIcon);
            }
        }

        return $categoryId;
    }

    protected function _getCategoryIdByOuterId($categoryOuterId = 0) {
        $categoryId = false;

        if (!empty($categoryOuterId)) {

            $categoryId = $this->categorys->getCategoryIdByOuterId($categoryOuterId);

            if (empty($categoryId)) {
//				$categoryId = $this->categorys->addCategory($parentId, $categoryOuterId, $categoryName, $categoryDescription);
//				
                if (empty($categoryId)) {
                    $this->api->log(3, sprintf('Method: %s, Line: %s => Category don\'t added!', __METHOD__, __LINE__));
//					continue;
                }
            }
        }

        return $categoryId;
    }

    /**
     * Update promotion item
     * 
     * @param array $item
     * 
     * @return integer
     */
    protected function _updatePromotion($item) {
        $promotionId = false;

        if (isset($item) && !empty($item) && is_array($item)) {
            $promotionOuterId = $item['id'];
            $promotionDescription = empty($item['description']) ? '' : $item['description'];
            $promotionPersent = $item['persent'];
            $promotionStatus = ($item['status'] == 1) ? 1 : 0;
            ;

            $promotionId = $this->promotions
                    ->getPromotionIdByOuterId($promotionOuterId);

            if (empty($promotionId)) {
                $promotionId = $this->promotions->addPromotion($promotionOuterId, $promotionDescription, $promotionPersent, $promotionStatus);

                if (empty($promotionId)) {
                    $this->api->log(3, sprintf('Method: %s, Line: %s => Promotion don\'t added!', __METHOD__, __LINE__));
//					continue;
                }
            } else {
                $this->promotions->editPromotion($promotionOuterId, $promotionDescription, $promotionPersent, $promotionStatus);
            }
        }

        return $promotionId;
    }

    /**
     * Update product item
     * 
     * @param array $product
     * @param integer $groupId
     * @param integer $deliverieId
     * @param integer $quantityId
     * 
     * @return integer
     */
    protected function _updateProduct($product, $groupId = 0, $deliverieId = 0, $quantityId = 0) {
        $productId = false;
        $groupId = (int) $groupId;

        if (isset($product) && !empty($product) && is_array($product) && !empty($groupId)) {
            $productArticul = $product['articul'];
            $productOuterId = $product['id'];
            $productName = $product['name'];
            $productDescription = empty($product['description']) ? '' : $product['description'];
            $productStatus = empty($product['status']) ? '' : (int) $product['status'];

            $productId = $this->products->getProductIdByArticul($productArticul);
            if (empty($productId)) {
                $productId = $this->products->addProduct((int) $deliverieId, $productName, $productDescription, $productArticul, $productStatus, (int) $productOuterId);

                if (empty($productId)) {
                    $this->api->log(3, sprintf('Method: %s, Line: %s => Product don\'t added!', __METHOD__, __LINE__));
//					continue;
                }
            } else {
                $this->products->editProduct($productId, (int) $deliverieId, $productName, $productDescription, $productStatus);
            }
        }

        return $productId;
    }

    /**
     * Update ProductGroup item
     * 
     * @param array $productGroup
     * @param integer $categoryId
     * 
     * @return integer
     */
    protected function _updateProductGroup($productGroup, $categoryId = 0) {
        $categoryId = (int) $categoryId;
        $productGroupId = false;

        if (isset($productGroup) && !empty($productGroup) && is_array($productGroup) && !empty($categoryId)) {

            if (isset($productGroup['name']) && !empty($productGroup['name'])) { // full data = create or update and get_id
                $productGroupName = $productGroup['name'];
                $productGroupDescription = empty($productGroup['description']) ? '' : $productGroup['description'];
                $productGroupStatus = empty($productGroup['status']) ? 0 : (int) $productGroup['status'];

                $productGroupOuterId = $productGroup['id'];
                $productGroupId = $this->groupProducts->getGroupIdByOuterId($productGroupOuterId);

                if (empty($productGroupId)) {
                    $productGroupId = $this->groupProducts->addGroupProduct($productGroupOuterId, $categoryId, $productGroupName, $productGroupDescription, $productGroupStatus);
                } else {
                    $this->groupProducts->editGroupProduct($productGroupId, $categoryId, $productGroupName, $productGroupDescription, $productGroupStatus);
                }
            } else {  // short data = only get_id
                $productGroupOuterId = $productGroup['id'];
                $productGroupId = $this->groupProducts->getGroupIdByOuterId($productGroupOuterId);
            }

            if (empty($productGroupId)) {
                $this->api->log(3, sprintf('Method: %s, Line: %s => Product group don\'t added!', __METHOD__, __LINE__));
                return;
            }
        }

        return $productGroupId;
    }

    /**
     * Update image item
     * 
     * @param string $name
     * @param integer $entity_type_id
     * @param integer $item_id
     * 
     * @return integer
     */
    protected function _updateImage($name, $entity_type_id = 0, $item_id = 0) {
        $imageId = false;

        if (isset($name) && !empty($name) && is_string($name)) {

            $imageId = $this->images->getImageIdByName($name);

            if (empty($imageId)) {
                $imageId = $this->images->addImage($name);

                if (empty($imageId)) {
                    $this->api->log(3, sprintf('Method: %s, Line: %s => Image don\'t added!', __METHOD__, __LINE__));
//					continue;
                }
            } else {
                $this->images->editImage($imageId, $name);
            }
        }

        return $imageId;
    }

    /**
     * Update video item
     * 
     * @param string $name
     * @param integer $entity_type_id
     * @param integer $item_id
     * 
     * @return integer
     */
    protected function _updateVideo($name, $entity_type_id = 0, $item_id = 0) {
        $videoId = false;

        if (isset($name) && !empty($name) && is_string($name)) {

            $videoId = $this->videos->getVideoIdByName($name);

            if (empty($videoId)) {
                $videoId = $this->videos->addVideo($name);
                if (empty($videoId)) {
                    $this->api->log(3, sprintf('Method: %s, Line: %s => Video don\'t added!', __METHOD__, __LINE__));
//					continue;
                }
            } else {
                $this->videos->editVideo($videoId, $name);
            }
        }

        return $videoId;
    }

    /**
     * Update quantity item
     * 
     * @param string $name
     * 
     * @return integer
     */
    protected function _updateQuantity($name) {
        $quantityId = false;

        if (isset($name) && !empty($name) && is_string($name)) {

            $quantityId = $this->quantities->getQuantityIdByName($name);

            if (empty($quantityId)) {
                $quantityId = $this->quantities->addQuantity($name);
                if (empty($quantityId)) {
                    $this->api->log(3, sprintf('Method: %s, Line: %s => Quantity don\'t added!', __METHOD__, __LINE__));
//					continue;
                }
            } else {
                $this->quantities->editQuantity($quantityId, $name);
            }
        }

        return $quantityId;
    }

    /**
     * Parse meta for item
     * 
     * @param array $items
     * @param integer $pageTypeId
     * @param integer $itemId
     */
    protected function _parseMeta($items, $pageTypeId = 0, $itemId = 0) {
        $pageTypeId = (int) $pageTypeId;
        $itemId = (int) $itemId;
        $pagemetaId = false;

        $metas = array();
        $metas['title'] = $this->api->getArrayNode($items['title']);
        $metas['description'] = $this->api->getArrayNode($items['description']);
        $metas['keywords'] = $this->api->getArrayNode($items['keywords']);

        // add for ids
        $pagemetaId = $this->_updateMetas($pageTypeId, $itemId, $metas);

        return $pagemetaId;
    }

    /**
     * Update Meta item
     * 
     * @param integer $pageTypeId
     * @param integer $itemId
     * @param integer $metaId
     * @param string $value
     */
    protected function _updateMeta($pageTypeId, $itemId, $metaId, $value) {
        $pagemetaId = false;

        // clear by ids
        $this->pagesmetas->clearByParams($pageTypeId, $itemId, $metaId);

        // add meta
        $pagemetaId = $this->pagesmetas->addMeta($pageTypeId, $itemId, $metaId, $value);

        if (empty($pagemetaId)) {
            $this->api->log(3, sprintf('Method: %s, Line: %s => Meta don\'t add!', __METHOD__, __LINE__));
//			continue;
        }

        return $pagemetaId;
    }

    /**
     * Update Meta items
     * 
     * @param integer $entityTypeId
     * @param integer $itemId
     * @param array $metas
     */
    protected function _updateMetas($entityTypeId, $itemId, $metas) {
        $pagemetaId = false;

        // clear by ids
        clsPagesmetas::getInstance()->clearByParams($entityTypeId, $itemId);

        if (!empty($metas)) {
            // add meta
            $pagemetaId = clsPagesmetas::getInstance()->addMetas($entityTypeId, $itemId, $metas);

            if (empty($pagemetaId)) {
                $this->api->log(3, sprintf('Method: %s, Line: %s => Metas don\'t add!', __METHOD__, __LINE__));
            }
        }
        
        return $pagemetaId;
    }

    protected function _parseProductDelivery($productDelivery) {
        $productDeliverieId = $this->deliveries->getIdByName($productDelivery);

        if (empty($productDeliverieId)) {
            $this->api->log(3, sprintf('Method: %s, Line: %s => Product delivery don\'t added!', __METHOD__, __LINE__));
            return;
        }

        return $productDeliverieId;
    }

    /**
     * Update block item
     * 
     * @param array $blocks
     * @param integer $parentId
     * 
     * @return integer
     */
    protected function _updateBlock($item) {
        $blockId = false;

        if (isset($item) && !empty($item) && is_array($item)) {
            $blockOuterId = $item['id'];
            $blockTitle = $item['title'];
            $blockDescription = $item['description'];
            $blockContent = $item['content'];
            $blockCreateDate = $item['create_date'];
            $blockStatus = ($item['status'] == 1) ? '1' : 0;

            $blockId = $this->blocks->getBlockIdByOuterId($blockOuterId);

            if (empty($blockId)) {
                $blockId = $this->blocks->addBlock($blockOuterId, $blockTitle, $blockDescription, $blockContent, $blockCreateDate, $blockStatus);

                if (empty($blockId)) {
                    $this->api->log(3, sprintf('Method: %s, Line: %s => Block don\'t added!', __METHOD__, __LINE__));
//					continue;
                }
            } else {
                $this->blocks->editBlock($blockOuterId, $blockTitle, $blockDescription, $blockContent, $blockCreateDate, $blockStatus);
            }
        }

        return $blockId;
    }
    
    /**
     * Update manufacturer
     * 
     * @param array $items
     * 
     * @return integer
     */
    protected function _updateManufacturer($items) {
        $manufacturerId = false;

        if (!empty($items) && is_array($items)) {
            foreach($items as $item) {
                $outerId = (int)$item['id'];
                $name = !empty($item['name']) ? $item['name'] : '';
                $description = !empty($item['description']) ? $item['description'] : '';
                $image = !empty($item['image']) ? $item['image'] : '';

                $manufacturerId = $this->manufacturers->getManufacturerIdByOuterId($outerId);

                if (empty($manufacturerId)) {
                    $manufacturerId = $this->manufacturers->addManufacturer($outerId, $name, $description, $image);

                    if (empty($manufacturerId)) {
                        $this->api->log(3, sprintf('Method: %s, Line: %s => Manufacturer don\'t added!', __METHOD__, __LINE__));
                    }
                } else {
                    $this->manufacturers->editManufacturer($outerId, $name, $description, $image);
                }
                
                self::$addedManufacturers[$outerId] = $manufacturerId;
            }
        }

        return $manufacturerId;
    }

    /**
     * Convert string to DateTime
     * 
     * @param string $datetime
     * 
     * @return datetime
     */
    public function _convert_datetime($datetime) {
        $datetime = new DateTime($datetime);
        return $datetime->format("Y-m-d H:i:s");
    }

    //TODO: these methods in another place

    public static function getEntityTypeIdByName($name) {
        // Lazy initialization takes place here
        if (!isset(self::$entity_types[$name])) {
            self::$entity_types[$name] = clsEntityTypes::getInstance()->getIdByName($name);
        }

        return self::$entity_types[$name];
    }

    public static function getPageTypeIdByName($name) {
        // Lazy initialization takes place here
        if (!isset(self::$page_types[$name])) {
            self::$page_types[$name] = clsPageTypes::getInstance()->getIdByName($name);
        }

        return self::$page_types[$name];
    }

    public static function getMetaTypeIdByName($name) {
        // Lazy initialization takes place here
        if (!isset(self::$meta_types[$name])) {
            self::$meta_types[$name] = clsMeta::getInstance()->getIdByName($name);
        }

        return self::$meta_types[$name];
    }

    public static function getProductDeliveryIdByName($name) {
        // Lazy initialization takes place here
        if (!isset(self::$product_delivery[$name])) {
            self::$product_delivery[$name] = clsDeliveries::getInstance()->apiGetIdByName($name);
        }

        return self::$product_delivery[$name];
    }

    public static function getProductIdByOuterId($id) {
        // Lazy initialization takes place here
        if (!isset(self::$addedProducts[$id])) {
            self::$addedProducts[$id] = clsProducts::getInstance()->getIdByOuterId((int)$id);
        }

        return self::$addedProducts[$id];
    }
     
    public static function getManufacturerIdByOuterId($id) {
        // Lazy initialization takes place here
        if (!isset(self::$addedManufacturers[$id])) {
            self::$addedManufacturers[$id] = clsManufacturers::getInstance()->getManufacturerIdByOuterId((int)$id);
        }

        return self::$addedManufacturers[$id];
    }
    
    public static function addSynonyms($entityTypeId, $id) {
        if (!empty(self::$synonyms)) {
            // clear by ids
            clsSynonyms::getInstance()->clearByCategoryId($entityTypeId, $id);

            $args = array('entity_type_id' => $entityTypeId,
                'item_id' => (int) $id);
            $this->apiSynonyms->setApi($this->api);
            foreach (clsApiParser::$synonyms as $value) {
                $this->apiSynonyms->parseItems($value, $args);
            }
            self::$synonyms = array();
        }
    }
    
    protected function _updateSynonyms($entityTypeId, $id, $synonyms) {
        if (!empty($synonyms) && is_array($synonyms) && !empty($entityTypeId) && !empty($id)) {
            // clear by ids
            $res = clsSynonyms::getInstance()->addSynonyms($entityTypeId, $id, $synonyms);
            
            if(empty($res)){
                $this->api->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Synonyms for category: id = %d, don\'t added!', __METHOD__, __LINE__, $id));
            }
        }
    }

    /**
     * Update User Companies
     * 
     * @param integer $userId
     * @param array $company
     */
    protected function _updateCompany($userId, $company) {
        $result = false;

        if (!empty($userId) && is_int($userId) && !empty($company) && is_array($company)) {

            $outerId = (int) $company['id'];
            $name = !empty($company['name']) ? $company['name'] : '';
            $address = !empty($company['address']) ? $company['address'] : '';
            $OGRN = !empty($company['OGRN']) ? $company['OGRN'] : '';
            $INN = !empty($company['INN']) ? $company['INN'] : '';
            $paymentMethod = !empty($company['payment_method']) ? $company['payment_method'] : '';
            $bankName = !empty($company['requisite']['bank']) ? $company['requisite']['bank'] : '';
            $currentAccount = !empty($company['requisite']['schet']) ? $company['requisite']['schet'] : '';
            $bik = !empty($company['requisite']['bik']) ? $company['requisite']['bik'] : '';
            $city = !empty($company['requisite']['city']) ? $company['requisite']['city'] : '';
            $correspondentAccount = !empty($company['correspondent_account']) ? $company['correspondent_account'] : '';
            $regDate = date('Y-m-d H:i:s', strtotime($company['reg_date']));
            $status = (int) $company['status'];

            $companyId = 0;
            do {
                $companyId = clsCompany::getInstance()->getCompanyIdByOuterId($outerId);

                if (!empty($companyId)) {
                    //update
                    break;
                }

                $companyId = clsCompany::getInstance()->addCompanyRaw($outerId, $name, $regDate, $status, $address, $OGRN, $INN, $paymentMethod, $bankName, $currentAccount, $bik, $city, $correspondentAccount);

                if (!empty($companyId)) {
                    break;
                }

                $this->api->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Company: outer_id = %s, don\'t added!', __METHOD__, __LINE__, $outerId));
            } while (0);

            if (!empty($userId) && !empty($companyId)) {
                $result = (int) $companyId;
            }
        }

        return $result;
    }

    /**
     * Update User Addresses
     * 
     * @param integer $userId
     * @param array $address
     */
    protected function _updateAddress($userId, $address) {
        $result = false;

        if (!empty($userId) && is_int($userId) && !empty($address) && is_array($address)) {

            $outerId = (int) $address['id'];
            $adr = $address['adr'];
            $status = (int) $address['status'];

            $resultAddress['id'] = $outerId;

            $addressId = 0;
            do {
                $addressId = clsAddresses::getInstance()->getAddressIdByOuterId($outerId);

                if (!empty($addressId)) {
                    break;
                }

                $addressId = clsAddresses::getInstance()->addAddressRaw($outerId, $adr, $status);

                if (!empty($addressId)) {
                    break;
                }

                $this->api->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Address: outer_id = %s, don\'t added!', __METHOD__, __LINE__, $outerId));
            } while (0);

            if (!empty($userId) && !empty($addressId)) {
                $result = (int) $addressId;
            }
        }

        return $result;
    }
    
    /**
     * Parse manufacturers porducts array
     * 
     * @param type $products
     */
    
    protected function _parseManufacturersProducts($products) {
        $productsForAdd = array();
        if(!empty($products) && is_array($products)) {
            foreach($products as $k => $v) {
                $productsForAdd[self::getProductIdByOuterId((int)$k)] = self::getManufacturerIdByOuterId((int)$v);
            }
            if(!empty($productsForAdd)) {
                clsProductsManufacturers::getInstance()->addManufacturers($productsForAdd);
            }
        }
    }

}