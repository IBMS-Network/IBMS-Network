<?php
	class clsApiActionBlocks {
		/**
		* Self instance 
		* 
		* @var clsApiActionBlocks
		*/
		static private $instance = NULL;

		/**
		* Api core
		* 
		* @var clsApiCore
		*/
		protected $api;

		/**
		* Constructor
		* 
		*/
		public function __construct() {
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
		* @var clsApiActionCategories
		*/
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new clsApiActionBlocks();
			}
			return self::$instance;
		} 

		public function action($items) {
			$result = array();
		   
			return $result;
		}
}