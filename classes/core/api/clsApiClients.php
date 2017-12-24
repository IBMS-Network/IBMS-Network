<?php

class clsApiClients extends clsApiParser {

    /**
     * Self instance 
     * 
     * @var clsApiClients
     */
    static private $instance = NULL;

    /**
     * Api core
     * 
     * @var clsApiCore
     */
    protected $api;

    /**
     * Users object
     * 
     * @var clsUser
     */
    protected $users;

    /**
     * Users Companies object
     * 
     * @var clsUsersCompanies
     */
    protected $usersCompanies;

    /**
     * Users Addresses object
     * 
     * @var clsUsersAddresses
     */
    protected $usersAddresses;

    /**
     * Users Socials Networks object
     * 
     * @var clsUsersSocialNetworks
     */
    protected $usersSocials;

    /**
     * Constructor
     * 
     */
    public function __construct() {
        $this->users = clsUser::getInstance();
        $this->usersCompanies = clsUsersCompanies::getInstance();
        $this->usersAddresses = clsUsersAddresses::getInstance();
        $this->usersSocials = clsUsersSocialNetworks::getInstance();
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
     * @var clsApiClients
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsApiClients();
        }
        return self::$instance;
    }

    public function parseItems($items, $args) {
        $result = array();

        if (!empty($items['client'])) {
            $users = $this->api->getArrayNode($items['client']);
            foreach ($users as $user) {
                if (empty($user['id'])) {
                    continue;
                }

                $outerId = (int) $user['id'];

                $firstName = empty($user['first_name']) ? '' : $user['first_name'];
                $lastName = empty($user['last_name']) ? '' : $user['last_name'];
                $fullName = $firstName . ' ' . $lastName;
                $email = empty($user['email']) ? '' : $user['email'];
                $password = empty($user['password']) ? '' : $user['password'];
                $phone = empty($user['phone']) ? '' : $user['phone'];
                $columnNumber = empty($user['price_level']) ? 0 : (int) $user['price_level'];
                $showDelivery = empty($user['show_delivery']) ? 0 : (int) $user['show_delivery'];
                $regDate = empty($user['reg_date']) ? '' : $this->_convert_datetime($user['reg_date']);
                $status = empty($user['status']) ? 0 : (int) $user['status'];
                $userTypeId = empty($user['client_type_id']) ? 0 : (int) $user['client_type_id'];

                $resultItem = &$result['client'][];
                $resultItem['id'] = $outerId;

                $userId = 0;
                do {
                    $userId = $this->users->getUserIdByOuterId($outerId);

                    if (!empty($userId)) {
                        break;
                    }

                    $userId = $this->users->createUserRaw($outerId, $firstName, $lastName, $fullName, $email, $password, $phone, $columnNumber, $showDelivery, $regDate, $status, $userTypeId
                    );

                    if (!empty($userId)) {
                        break;
                    }

                    $this->api->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Client: outer_id = %s, don\'t added!', __METHOD__, __LINE__, $outerId));
                } while (0);

                if (!empty($userId)) {
                    if (!empty(clsApiParser::$userCompanies)) {
                        $companies = array();
                        foreach (clsApiParser::$userCompanies as $company) {
                            $companyId = 0;
                            $companyId = $this->_updateCompany((int) $userId, $company);
                            if (!empty($companyId)) {
                                $companies[] = $companyId;
                            }
                        }
                        if (!empty($companies)) {
                            $this->usersCompanies->clearCompanysByUserId($userId);
                            $this->usersCompanies->addUserCompanies($companies, $userId);
                        }
                        clsApiParser::$userCompanies = array();
                    }
                    if (!empty(clsApiParser::$userAddresses)) {
                        $addresses = array();
                        foreach (clsApiParser::$userAddresses as $address) {
                            $addressId = 0;
                            $addressId = $this->_updateAddress((int) $userId, $address);
                            if (!empty($addressId)) {
                                $addresses[] = $addressId;
                            }
                        }
                        if (!empty($addresses)) {
                            $this->usersAddresses->clearAddressesByUserId($userId);
                            $this->usersAddresses->addUserAddresses($addresses, $userId);
                        }
                        clsApiParser::$userAddresses = array();
                    }
                    if (!empty($user['socials']) && is_array($user['socials'])) {
                        $this->usersSocials->clearSocialsByUserId($userId);
                        $this->usersSocials->addUserSocials($user['socials'], $userId);
                    }
                }

                $resultItem += $this->api->callParser($user, array(
                    'user_id' => $userId
                        ));

                $resultItem['status'] = !empty($userId) ? '1' : '0';
            }
        }

        return $result;
    }

}