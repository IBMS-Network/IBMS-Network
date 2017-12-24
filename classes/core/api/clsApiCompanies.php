<?php

class clsApiCompanies {

    /**
     * Self instance 
     * 
     * @var clsApiCompanies
     */
    static private $instance = NULL;

    /**
     * Api core
     * 
     * @var clsApiCore
     */
    protected $api;

    /**
     * Companys object
     * 
     * @var clsCompany
     */
    protected $companys;

    /**
     * Users Companies object
     * 
     * @var clsUsersCompanies
     */
    protected $usersCompanies;

    /**
     * Constructor
     * 
     */
    public function __construct() {
        $this->companys = clsCompany::getInstance();
        $this->usersCompanies = clsUsersCompanies::getInstance();
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
            self::$instance = new clsApiCompanies();
        }
        return self::$instance;
    }

    public function parseItems($items, $args) {
        $result = array();
        $userId = (int) $args['user_id'];

        $companys = $this->api->getArrayNode($items['company']);
        foreach ($companys as $company) {
            $resultCompany = &$result['company'][];

            $outerId = (int) $company['id'];

            $resultCompany['id'] = $outerId;

            $name = !empty($company['name']) ? $company['name'] : '';
            $address = !empty($company['address']) ? $company['address'] : '';
            $OGRN = !empty($company['OGRN']) ? $company['OGRN'] : '';
            $INN = !empty($company['INN']) ? $company['INN'] : '';
            $paymentMethod = !empty($company['payment_method']) ? $company['payment_method'] : '';
            $bankName = !empty($company['requisite']['bank']) ? $company['requisite']['bank'] : '';
            $currentAccount = !empty($company['requisite']['schet']) ? $company['requisite']['schet'] : '';
            $bik = !empty($company['requisite']['bik']) ? $company['requisite']['bik'] : '';
            $city = !empty($company['requisite']['city']) ? $company['requisite']['city'] : '';
            $correspondentAccount = !empty($company['correspondent_account']) ? $company['correspondent_account'] : '';
            $regDate = date('Y-m-d H:i:s', strtotime($company['reg_date']));
            $status = (int) $company['status'];

            $companyId = 0;
            do {
                $companyId = $this->companys->getCompanyIdByOuterId($outerId);

                if (!empty($companyId)) {
                    //update
                    break;
                }

                $companyId = $this->companys->addCompanyRaw($outerId, $name, $regDate, $status,
                        $address, $OGRN, $INN, $paymentMethod, $bankName, $currentAccount, $bik,
                        $city, $correspondentAccount);

                if (!empty($companyId)) {
                    break;
                }

                $this->api->log(API_LOG_TYPE_ERROR, sprintf('Method: %s, Line: %s => Company: outer_id = %s, don\'t added!', __METHOD__, __LINE__, $outerId));
            } while (0);

            if (!empty($userId) && !empty($companyId)) {
                clsApiParser::$userCompanies[] = $companyId;
//                $this->usersCompanies->addUserCompany($companyId, $userId);
            }

            $resultCompany += $this->api->callParser($company, array(
                'user_id' => $userId,
                'company_id' => $companyId
                    ));

            $resultCompany['status'] = !empty($companyId) ? '1' : '0';
        }
        return $result;
    }

}