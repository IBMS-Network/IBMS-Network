<?php

class clsApiAddresses {

    /**
     * Self instance 
     * 
     * @var clsApiAddresses
     */
    static private $instance = NULL;

    /**
     * Api core
     * 
     * @var clsApiCore
     */
    protected $api;

    /**
     * Addresses object
     * 
     * @var clsAddresses
     */
    protected $addresses;

    /**
     * Users Addresses object
     * 
     * @var clsUsersAddresses
     */
    protected $usersAddresses;

    /**
     * Constructor
     * 
     */
    public function __construct() {
        $this->addresses = clsAddresses::getInstance();
        $this->usersAddresses = clsUsersAddresses::getInstance();
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
     * @var clsApiCompanies
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiAddresses();
        }
        return self::$instance;
    }

    public function parseItems($items, $args) {
        $result = array();
        $userId = (int) $args['user_id'];

        $addresses = $this->api->getArrayNode($items['address']);
        foreach ($addresses as $address) {
            $resultAddress = &$result['address'][];

            $outerId = (int) $address['id'];
            $adr = $address['adr'];
            $status = (int) $address['status'];

            $resultAddress['id'] = $outerId;

            $addressId = 0;
            do {
                $addressId = $this->addresses->getAddressIdByOuterId($outerId);

                if (!empty($addressId)) {
                    break;
                }

                $addressId = $this->addresses->addAddressRaw($outerId, $adr, $status);

                if (!empty($addressId)) {
                    break;
                }

                $this->api->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Address: outer_id = %s, don\'t added!', __METHOD__, __LINE__, $outerId));
            } while (0);

            if (!empty($userId) && !empty($addressId)) {
                clsApiParser::$userAddresses[] = $addressId;
            }

            $resultAddress += $this->api->callParser($address, array(
                'user_id' => $userId,
                'address_id' => $addressId
                    ));

            $resultAddress['status'] = !empty($addressId) ? '1' : '0';
        }
        return $result;
    }

}