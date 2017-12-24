<?php

namespace classes;

use classes\core\clsDB;
use classes\core\clsCommon;
use engine\clsSysServices;

class clsCommonService
{

    /**
     * Database
     * 
     * @var DB
     */
    protected $em = false;

    /**
     * domain config array
     * @var array $config - domain config array
     */
    protected $config = array();

    /**
     * Id current service item
     * 
     * @var integer
     */
    public $serviceId = 0;

    /**
     * Constructor 
     * 
     * @return clsCommonService
     */
    function __construct()
    {
        $this->em = clsDB::getInstance();
        $this->config = clsCommon::getDomainConfig();
    }

    /**
     * Set value serviceId for service by name
     * 
     * @return integer
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Set value serviceId for service by name
     * 
     * @param string $serviceName
     * 
     * @return boolean
     */
    public function setServiceId($serviceName = '')
    {
        if (!empty($serviceName)) {
            $this->serviceId = (int) clsSysServices::getInstance()->getServiceIdByNames($serviceName);
        }
        return true;
    }

}
