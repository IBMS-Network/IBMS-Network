<?php
	class clsApiActionPages {
		/**
		* Self instance 
		* 
		* @var clsApiActionPages
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
		* @var clsApiActionPages
		*/
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new clsApiActionPages();
			}
			return self::$instance;
		} 

		public function action($items) {
			$result = array();
			return $result;
		}
}