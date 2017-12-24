<?php
	class clsApiBlocks extends clsApiParser{
		/**
		* Self instance 
		* 
		* @var clsApiBlocks
		*/
		static private $instance = NULL;
				
		/**
		* Api core
		* 
		* @var clsApiCore
		*/
		protected $api;
		
		/**
		* Blocks object
		* 
		* @var clsBlocks
		*/
		protected $blocks;
		
		/**
		* Constructor
		* 
		*/
		public function __construct() {
			$this->blocks = clsDynamicBlocks::getInstance();
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
		* @var clsApiBlocks
		*/
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new clsApiBlocks();
			}
			return self::$instance;
		} 
		
		public function parseItems($items, $args) {
			$result = array();
			$blocks = $this->api->getArrayNode($items['block']);
			
			foreach ($blocks as $item) {
				// update data
				$blockId = $this->_updateBlock($item, 0);
				
			}
                        
			return $result;
		}
	}