<?php
	class clsApiActionOutClients {
		/**
		* Self instance 
		* 
		* @var clsApiActionOutClients
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
		* @var clsApiActionOutClients
		*/
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new clsApiActionOutClients();
			}
			return self::$instance;
		} 

		public function action($items) {
			$result = array();
			$users = clsUser::getInstance();
			$companys = clsCompany::getInstance();
			$addresses = clsAddresses::getInstance();
			$session = clsSession::getInstance();
			if (($usersList = $users->getAll())) {
				foreach ($usersList as $user) {
					$user['client_type_id']  = $user['user_type_id'];
					$user['price_level']  = $user['column_number'];
					
                    if(($socialsList = $users->getUserNetworksFull((int)$user['id']))) {
                        foreach($socialsList as $iKey => $aSocial) {
							$socials[$aSocial['name'] . 'ID'] = $aSocial['user_network_id'];
							$socials[$aSocial['name'] . 'Token'] = $aSocial['token'];
						}
						$user['socials'] = $socials;
                    }
                    
                    if (($companysList = $companys->getCompaniesByUserId((int)$user['id']))) {
						foreach($companysList as $iKey => $aCompany) {
							$companysList[$iKey]['requisite']['city'] = $aCompany['city'];
							$companysList[$iKey]['requisite']['bank'] = $aCompany['bank_name'];
							$companysList[$iKey]['requisite']['bik'] = $aCompany['BIK'];
							$companysList[$iKey]['requisite']['schet'] = $aCompany['current_account'];
                            
                            unset($companysList[$iKey]['current_account']);
                            unset($companysList[$iKey]['BIK']);
                            unset($companysList[$iKey]['bank_name']);
                            unset($companysList[$iKey]['city']);
                            unset($companysList[$iKey]['outer_id']);
                            
                            $user['companies']['company'][] = $companysList[$iKey];
						}
					}
                    
					if (($addressesList = $addresses->getAddressesByUserId((int)$user['id']))) {
						foreach($addressesList as $iKey => $aAddress) {
							$addressesList[$iKey]['adr'] = $aAddress['address'];
							unset($addressesList[$iKey]['address']);
							unset($addressesList[$iKey]['outer_id']);
							unset($addressesList[$iKey]['reg_date']);
                            $user['addresses']['address'][] = $addressesList[$iKey];
						}
					}
					
                    unset($user['address']);
                    unset($user['column_number']);
					unset($user['outer_id']);
					unset($user['user_class_id']);
					unset($user['users_costs_id']);
										
					$result[] =array('clients', 'client', $user);
				}
			}
			return $result;
		}
}