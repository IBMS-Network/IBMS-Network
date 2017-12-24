<?php

namespace engine;

use Doctrine\ORM\Internal\Hydration\ObjectHydrator;
use engine\clsSysSession;
use classes\clsSession;

class clsSysStorage
{
    
    private static $instance = NULL;
    
    /**
     * Object storage
     */
    protected $_storage = NULL;
    
    public function __construct()
    {
        $this->initStorage();
    }
    
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsSysStorage();
        }
        return self::$instance;
    }
    
    /**
     * Init storage data engine 
     * 	for use on project
     * 
     * @return Object
     */
    public function initStorage()
    {
        $adapterClassName = '';
        if (constant('SESSION_ADAPTER')) {
            $adapterClassName = 'engine\\session\\adapter\\cls' . ucfirst(SESSION_ADAPTER) . 'Adapter';
        }
        if (class_exists($adapterClassName)) {
            $this->_storage = new $adapterClassName();
        } else {
            session_start();
            if (clsSysCommon::isProjectOn()) {
                $this->_storage = new clsSession();
            } else {
                $this->_storage = new clsSysSession();
            }
        }
        
        return $this->_storage;
    }
    
    /**
     * Get param from session array
     * 
     * @param string $param
     * 	key in assoc session array for checking. ex : $_SESSION[$param]
     * @param string $prefix
     * 	key for 1 lvl in checking. ex : $_SESSION[$prefix][$param] 
     * 
     * @return mixed
     * 	data from session array by key or FALSE
     */
    public function getParam($param, $prefix = '')
    {
        return $this->_storage->getParam($param, $prefix);
    }
    
    /**
     * Clear param from session array
     * 
     * @param string $param
     * key in assoc session array for checking. ex : $_SESSION[$param]
     * @param string $prefix
     * key for 1 lvl in checking. ex : $_SESSION[$prefix][$param]
     */
    public function clearParam($param, $prefix = "")
    {
        return $this->_storage->clearParam($param, $prefix);
    }
    
    /**
     * Clear params from session array
     * 
     * @param string $prefix
     * key for 1 lvl in checking. ex : $_SESSION[$prefix]
     * 
     * @return bool
     */
    public function clearParams($prefix = '')
    {
        return $this->_storage->clearParams($prefix);
    }
    
    /**
     * Set array of data to session array
     * 
     * @param array $sess_arr
     * set array to session array
     * @param string $prefix
     * key for 1 lvl in checking. ex : $_SESSION[$prefix][$param] 
     * 
     * @return bool
     */
    public function setParams(array $sess_arr = array(), $prefix = '')
    {
        return $this->_storage->setParams($sess_arr, $prefix);
    }
    
    /**
     * Set param in session array
     * 
     * @param string $param
     * key in assoc session array for checking. ex : $_SESSION[$param]
     * @param mixed $value
     * value of param to set to session array
     * @param string $prefix
     * key for 1 lvl in checking. ex : $_SESSION[$prefix][$param]
     * 
     * @return bool
     */
    public function setParam($param, $value, $prefix = '')
    {
        return $this->_storage->setParam($param, $value, $prefix);
    }
    
    /**
     * Check if session param is set
     * 
     * @param string $param
     * key in assoc session array for checking. ex : $_SESSION[$param]
     * @param string $prefix
     * key for 1 lvl in checking. ex : $_SESSION[$prefix][$param] 
     * 
     * @return bool
     */
    public function isParamSet($param, $prefix = '')
    {
        return $this->_storage->isParamSet($param, $prefix);
    }
    
    /**
     * @TODO : check need exists
     * Set prefix for session attributes
     * 
     * @param string $prefix
     * key for 1 lvl in checking. ex : $_SESSION[$prefix]
     */
    public function setPrefix($prefix = "")
    {
        $this->_prefix = !empty($prefix) ? $prefix : '';
    }
    
    /**
     * @TODO : check need exists
     * Get prefix for session attributes
     * 
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

}