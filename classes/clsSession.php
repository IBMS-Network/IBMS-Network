<?php

namespace classes;

use engine\clsSysSession;

class clsSession extends clsSysSession {

    private static $instance = NULL;

    public function __construct(){
        $this->objEvent = clsEvent::getInstance();
    }

    public static function getInstance(){
        if( self::$instance == NULL ){
            self::$instance = new clsSession();
        }
        return self::$instance;
    }

    /**
     * Method to get active user' session data
     * @return array|null
     */
    public function getUserSession(){
        return $this->getParam('user-data', 'auth');
    }
    
    /**
     * Add product to cart
     * 
     * @param array $products
     * @return array
     */
    public function addToCartSession($products)
    {
        $result = array('result' => false);
        if(!empty($products) && is_array($products)) {
            foreach($products as $v) {
                $id = (int)$v['productId'];
                $cnt = isset($v['productCount']) ? (int)$v['productCount'] : 1;

                if(!empty($id)) {
                    if($cnt == 0) {
                        unset($_SESSION['cart'][$id]);
                    } else {
                        $_SESSION['cart'][$id]['count'] = $cnt;
                    }
                }
            }

            $result['result'] = true;
        }

        return $result;
    }
    
    /**
     * Get cart from session
     * 
     * @return array
     */
    public function getCart()
    {
        return $this->getParam('cart');
    }
    
    /**
     * Check empty cart from session
     * 
     * @return boolean
     */
    public function isCartEmpty()
    {
        return empty($_SESSION['cart']) ? true : false;
    }
    
    /**
     * Clear cart from session
     * 
     * @return void
     */
    public function clearCart()
    {
        $this->clearParams('cart');
    }
    
    /**
     * Set last order ID 
     * 
     * @param integer $orderId
     * @return void
     */
    public function setOrderId($orderId)
    {
        $this->setParam('order-id', (int)$orderId, 'order');
    }
    
    /**
     * Get last order ID 
     * 
     * @return integer
     */
    public function getOrderId()
    {
        return $this->getParam('order-id', 'order');
    }
}