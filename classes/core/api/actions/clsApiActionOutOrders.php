<?php
	class clsApiActionOutOrders {
		/**
		* Self instance 
		* 
		* @var clsApiActionOutOrders
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
		* @var clsApiActionOutOrders
		*/
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new clsApiActionOutOrders();
			}
			return self::$instance;
		} 

		public function action($items) {
			$result = array();

            $users = clsUser::getInstance();
			$orders = clsOrder::getInstance();
			
			if (($ordersList = $orders->getAll())) {
				foreach ($ordersList as $order) {
					$order['status'] = $order['status'];
					$order['order_status'] = $order['order_status_id'];
					
					if (($userInfo = $users->getUserById($order['user_id']))) {
						$order['clients']['client'] = array(
							'id' => $userInfo['id'],
							'first_name' => $userInfo['first_name'],
							'last_name' => $userInfo['last_name'],
							'phone' => $userInfo['phone'],
							'email' => $userInfo['email'],
						);
					}
					
					//$aOrder['client']['client'] = array();

					unset($order['outer_id']);
					unset($order['user_id']);
					unset($order['client_id']);
					unset($order['order_email_activate']);
					unset($order['order_status_id']);
					unset($order['comments']);
						
					$result[] = array('orders', 'order', $order);
				}
			}
			return $result;
		}
}