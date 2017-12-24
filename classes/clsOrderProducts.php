<?php

class clsOrderProducts {

	static private $instance = NULL;
	private $db = "";

	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsOrderProducts();
		}
		return self::$instance;
	}

	public function clsOrderProducts() {
		$this->db = DB::getInstance();
	}
	
	public function clearProductsByOrderId($orderId) {
		$sql = "DELETE FROM orderproducts WHERE  order_id = ?";
		$sqlArr = array($orderId);
		$this->db->Execute($sql, $sqlArr);
	}
	
	public function addProduct($iOrderId, $iDeliveryId, $iProductId, $iQuantity, $iPrice) {
		$sql = "INSERT INTO orderproducts(order_id, delivery_id, product_id, quantity, price)
			VALUES(?,?,?,?,?)";
		$sqlArr = array($iOrderId, $iDeliveryId, $iProductId, $iQuantity, $iPrice);
		$this->db->Execute($sql, $sqlArr);
		return $this->db->Insert_ID();
	}
}