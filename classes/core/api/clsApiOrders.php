<?php
class clsApiOrders extends clsApiParser {
	/**
	* Self instance 
	* 
	* @var clsApiOrders
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
	* @var clsApiOrders
	*/
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsApiOrders();
		}
		return self::$instance;
	} 

	public function parseItems($items, $args) {
		$result = array();
		
		$orders = clsOrder::getInstance();
		
        if(isset($items['order'])) {
		//if ($this->api->checkAction('orders')) {
			$nodeKey = 'order';
			$nodeData = $this->api->getArrayNode($items[$nodeKey]);

			foreach ($nodeData as $nodeItem) {
				if (empty($nodeItem['id'])) {
					continue;
				}
				
				$outerId = (int)$nodeItem['id'];
				$resultNode = &$result[$nodeKey][];
				$resultNode['id'] = $outerId;
				
				$userId = empty($nodeItem['user_id']) ? 0 : (int)$nodeItem['user_id'];
				$orderStatus = empty($nodeItem['order_status']) ? 0 : (int)$nodeItem['order_status'];
				$companyId = empty($nodeItem['company_id']) ? 0 : (int)$nodeItem['company_id'];
				$summary = empty($nodeItem['summary']) ? 0 : (float)$nodeItem['summary'];
				$createDate = empty($nodeItem['create_date']) ? '' : $this->_convert_datetime($nodeItem['create_date']);
				$deliveryDate = empty($nodeItem['delivery_date']) ? '' : $this->_convert_datetime($nodeItem['delivery_date']);
				$status = empty($nodeItem['status']) ? 0 :(int)$nodeItem['status'];
				
				if($orderStatus == ORDER_STATUS_COMPLETE) {
					$events = clsEvent::getInstance();
					$events->addCompleteOrder($userId);
				}
				
				$itemId = 0;
				do {
					$itemId = $orders->getOrderIdByOuterId($outerId);

					if (!empty($itemId)) {
						break;
					}

					$itemId = $orders->createOrderRaw($outerId, $userId, $orderStatus, $companyId, $summary,
						$createDate, $deliveryDate, $status);

					if (!empty($itemId)) {
						break;
					}

					$this->api->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Order: outer_id = %s, don\'t added!', __METHOD__, __LINE__, $outerId));
				} while(0);
				
				$resultNode += $this->api->callParser($nodeItem, array(
					'from' => $nodeKey,
					'item_id' => $itemId
				));

				$resultNode['status'] = !empty($itemId) ? '1' : '0';
			}
		//}
        }

		return $result;
	}
}