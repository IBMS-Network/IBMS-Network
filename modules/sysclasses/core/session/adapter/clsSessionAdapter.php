<?php

namespace engine\session\adapter;

use engine\clsSysCommon;
use engine\clsSysSession;
use classes\clsSession;

/**
 * This class is standard SESSION adapter
 */
class clsSessionAdapter extends clsAbstractAdapter
{
    private $_session = null;
    
    public function __construct()
    {
        if (clsSysCommon::isProjectOn()) {
            $this->_session = new clsSession();
        } else {
            $this->_session = new clsSysSession();
        }
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
    public function getParam($param, $prefix = '') {
        return $this->_session->getParam($param, $prefix);
    }
    
    /**
     * Clear param from session array
     * 
     * @param string $param
     * 	key in assoc session array for checking. ex : $_SESSION[$param]
     * @param string $prefix
     * 	key for 1 lvl in checking. ex : $_SESSION[$prefix][$param]
     * @return boolean
     */
    public function clearParam($param, $prefix = "") {
        return $this->_session->clearParam($param, $prefix);
    }
    
    /**
     * Clear params from session array
     * 
     * @param string $prefix
     * 	key for 1 lvl in checking. ex : $_SESSION[$prefix]
     * 
     * @return bool
     */
    public function clearParams($prefix = '') {
        return $this->_session->clearParams($prefix);
    }
    
    /**
     * Set array of data to session array
     * 
     * @param array $sess_arr
     * 	set array to session array
     * @param string $prefix
     * 	key for 1 lvl in checking. ex : $_SESSION[$prefix][$param] 
     * 
     * @return bool
     */
    public function setParams($sess_arr = array(), $prefix = '') {
        return $this->_session->setParams($sess_arr, $prefix);
    }
    
    /**
     * Set param in session array
     * 
     * @param string $param
     * 	key in assoc session array for checking. ex : $_SESSION[$param]
     * @param mixed $value
     * 	value of param to set to session array
     * @param string $prefix
     * 	key for 1 lvl in checking. ex : $_SESSION[$prefix][$param]
     * 
     * @return bool
     */
    public function setParam($param, $value, $prefix = '') {
        return $this->_session->setParam($param, $value, $prefix);
    }
    
    /**
     * Check if session param is set
     * 
     * @param string $param
     * 	key in assoc session array for checking. ex : $_SESSION[$param]
     * @param string $prefix
     * 	key for 1 lvl in checking. ex : $_SESSION[$prefix][$param] 
     * 
     * @return bool
     */
    public function isParamSet($param, $prefix = '') {
        return $this->_session->isParamSet($param, $prefix);
    }
    
    /**
     * Set prefix for session attributes
     * 
     * @param string $prefix
     * 	key for 1 lvl in checking. ex : $_SESSION[$prefix]
     */
    public function setPrefix($prefix = "") {
        $this->_prefix = !empty($prefix) ? $prefix : '';
    }
    
    /**
     * Get prefix for session attributes
     * 
     * @return string
     */
    public function getPrefix() {
        return $this->_prefix;
    }

}
