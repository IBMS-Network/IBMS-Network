<?php
	class clsApiPromotions extends clsApiParser{
		/**
		* Self instance 
		* 
		* @var clsApiPromotions
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
		* @var clsPromotions
		*/
		protected $promotions;

		/**
		* Promotion products
		* 
		* @var clsProductPromotion
		*/
		protected $productPromotion;

		/**
		* Products object
		* 
		* @var clsProducts
		*/
		protected $products;


		/**
		* Constructor
		* 
		*/
		public function __construct() {
			$this->promotions = clsPromotions::getInstance();
			$this->productPromotion = clsProductPromotion::getInstance();
			$this->products = clsProducts::getInstance();
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
		* @var clsApiPromotions
		*/
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new clsApiPromotions();
			}
			return self::$instance;
		} 

		public function parseItems($items, $args) {
			$result = array();

			$promotions = $this->api->getArrayNode($items['promotion']);

			foreach ($promotions as $item) {
                // update data
                $promotionId = $this->_updatePromotion($item);
                
                // clear all products for $promotionId
                $this->productPromotion->deleteProductsByPromotionId($promotionId);
                
                // get products id list
                $products = $this->api->getArrayNode($item['products']);
                
                // insert ($promotionId, $product);
//                $ids = array();
//                foreach ($products[0]['product_id'] As $product){
//                    $ids[] = $product['product_id'];
//                }
                $productIds = array();
                if(!empty($products[0]['product_id'])) {
                    $productIds = $this->products->getIdsByOuterIds($products[0]['product_id']);
                }
                if($productIds) {
                    $ids = array();
                    foreach($productIds as $productId) {
                        $ids[] = $productId['id'];
                    }
                    foreach($ids as $id) {
                        $this->productPromotion->addProduct($id, $promotionId);
                    }
                }
            }
            return $result;
		}
}