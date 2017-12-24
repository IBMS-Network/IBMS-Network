<?php
	class clsApiActionNews {
		/**
		* Self instance 
		* 
		* @var clsApiActionNews
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
		* @var clsApiActionNews
		*/
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new clsApiActionNews();
			}
			return self::$instance;
		} 

		public function action($items) {
			$result = array();
			return array(
				array('news', 'news', $result)
			);
		}
}