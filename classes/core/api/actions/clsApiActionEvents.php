<?php

class clsApiActionEvents {

    /**
     * Self instance 
     * 
     * @var clsApiActionEvents
     */
    static private $instance = NULL;

    /**
     * Api core
     * 
     * @var clsApiCore
     */
    protected $api;

    /**
     * Events class
     * 
     * @var clsEvent
     */
    protected $events;

    /**
     * Users class
     * 
     * @var clsUser
     */
    protected $users;

    /**
     * Orders class
     * 
     * @var clsOrder
     */
    protected $orders;

    /**
     * Companys class
     *
     * @var clsCompany
     */
    protected $companys;
    
    /**
     * Addresses class
     *
     * @var clsAddresses
     */
    protected $addresses;

    /**
     * Constructor
     * 
     */
    public function __construct() {
        $this->events = clsEvent::getInstance();
        $this->companys = clsCompany::getInstance();
        $this->addresses = clsAddresses::getInstance();
        $this->users = clsUser::getInstance();
        $this->orders = clsOrder::getInstance();
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
     * @var clsApiProducts
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiActionEvents();
        }
        return self::$instance;
    }

    /**
     * Get events array
     * 
     * @return array
     */
    public function action($data) {
        $result = array();
        $events = $this->events->getEventsList();
        foreach ($events as $event) {
            if (empty($event['name'])) {
                continue;
            }
            $eventType = $event['name'];

            $handlerName = sprintf('_getEvent%sData', ucfirst($eventType));
            $callback = array($this, $handlerName);
            if (is_callable($callback)) {
                $result[] = call_user_func($callback, $event);
            } else {
                $this->api->log(2, sprintf('Method: %s, Line: %s => Handler: "%s::%s()" don\'t exists!', __METHOD__, __LINE__, __CLASS__, $handlerName));
            }
        }

        return $result;
    }

    private function _getEventUserData($event) {
        if (($result = $this->users->getUserById($event['entity_id']))) {
            $result['client_type_id'] = $result['user_type_id'];
            $result['price_level'] = $result['column_number'];

            if (($socialsList = $this->users->getUserNetworksFull((int) $result['id']))) {
                foreach ($socialsList as $iKey => $aSocial) {
                    $socials[$aSocial['name'] . 'ID'] = $aSocial['user_network_id'];
                    $socials[$aSocial['name'] . 'Token'] = $aSocial['token'];
                }
                $result['socials'] = $socials;
            }

            if (($companysList = $this->companys->getCompaniesByUserId((int) $result['id']))) {
                foreach ($companysList as $iKey => $aCompany) {
                    $companysList[$iKey]['requisite']['city'] = $aCompany['city'];
                    $companysList[$iKey]['requisite']['bank'] = $aCompany['bank_name'];
                    $companysList[$iKey]['requisite']['bik'] = $aCompany['BIK'];
                    $companysList[$iKey]['requisite']['schet'] = $aCompany['current_account'];

                    unset($companysList[$iKey]['current_account']);
                    unset($companysList[$iKey]['BIK']);
                    unset($companysList[$iKey]['bank_name']);
                    unset($companysList[$iKey]['city']);
                    unset($companysList[$iKey]['outer_id']);

                    $result['companies']['company'][] = $companysList[$iKey];
                }
            }

            if (($addressesList = $this->addresses->getAddressesByUserId((int) $result['id']))) {
                foreach ($addressesList as $iKey => $aAddress) {
                    $addressesList[$iKey]['adr'] = $aAddress['address'];
                    unset($addressesList[$iKey]['address']);
                    unset($addressesList[$iKey]['outer_id']);
                    unset($addressesList[$iKey]['reg_date']);
                    $result['addresses']['address'][] = $addressesList[$iKey];
                }
            }

            unset($result['address']);
            unset($result['column_number']);
            unset($result['outer_id']);
            unset($result['user_class_id']);
            unset($result['users_costs_id']);

            $this->events->updateStatusFlag($event['id'], 1);
        } else {
            $result = array();
        }
        return array('clients', 'client', $result);
    }

    private function _getEventOrderData($event) {
        $result = array();

        if (($order = $this->orders->getOrderById($event['entity_id']))) {
            $order['status'] = $order['status'];
            $order['order_status'] = $order['order_status_id'];

            if (($userInfo = $this->users->getUserById($order['user_id']))) {
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

            $result = $order;

            $this->events->updateStatusFlag($event['id'], 1);
        }

        return array('orders', 'order', $result);
    }

}