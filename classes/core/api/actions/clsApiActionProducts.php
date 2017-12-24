<?php
	class clsApiActionProducts {
		/**
		* Self instance 
		* 
		* @var clsApiActionProducts
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
		* @var clsApiActionProducts
		*/
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new clsApiActionProducts();
			}
			return self::$instance;
		} 

		public function action($items) {
			$result = array();
			return $result;
		}
}