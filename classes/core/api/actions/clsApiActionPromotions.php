<?php
	class clsApiActionPromotions {
		/**
		* Self instance 
		* 
		* @var clsApiActionPromotions
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
		* @var clsApiActionPromotions
		*/
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new clsApiActionPromotions();
			}
			return self::$instance;
		} 

		public function action($items) {
			$result = array();
			return $result;
		}
}