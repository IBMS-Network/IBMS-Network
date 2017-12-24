<?php

namespace engine;

class clsSysSession {
    
    private static $instance = NULL;
    
    /**
    * Inner session prefix for an object
    */
    private $_prefix = '';

    public function __construct() {
    }
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsSysSession();
        }
        return self::$instance;
    }
    
    public static function saveLastVisitedURL() {
        if (isset($_SERVER['REQUEST_URI']) && !in_array($_SERVER['REQUEST_URI'], array('/authorization/', '/favicon.ico', '/images/favicon.ico', '/?err=11')))
            $_SESSION["user_last_visited_uri"] = $_SERVER['REQUEST_URI'];
    }
    
    public static function getLastVisitedURL() {
        if (!empty($_SESSION["user_last_visited_uri"])) {
            
            return $_SESSION["user_last_visited_uri"];
        }
        
        return false;
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
        if (!empty($param)) {
            return !empty($prefix) ? ($this->isParamSet($param, $prefix) ? $_SESSION[$prefix][$param] : false) : ($this->isParamSet($param) ? $_SESSION[$param] : false);
        } else {
            return false;
        }
    }
    
    /**
     * Clear param from session array
     * 
     * @param string $param
     * 	key in assoc session array for checking. ex : $_SESSION[$param]
     * @param string $prefix
     * 	key for 1 lvl in checking. ex : $_SESSION[$prefix][$param]
     * @return bool
     */
    public function clearParam($param, $prefix = "") {
        if ($this->isParamSet($param, $prefix)) {
            if (!empty($prefix)) {
                $_SESSION[$prefix][$param] = NULL;
                unset($_SESSION[$prefix][$param]);
            } else {
                $_SESSION[$param] = NULL;
                unset($_SESSION[$param]);
            }
        }
        return true;
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
        if (!empty($prefix)) {
            $_SESSION[$prefix] = array();
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Set array of data to session array
     * 
     * @param array $sessArr
     * 	set array to session array
     * @param string $prefix
     * 	key for 1 lvl in checking. ex : $_SESSION[$prefix][$param] 
     * 
     * @return bool
     */
    public function setParams(array $sessArr = array(), $prefix = '') {
        if (!empty($sessArr) && is_array($sessArr)) {
            foreach ( $sessArr as $key => $value ) {
                $this->setParam($key, $value, $prefix);
            }
            return true;
        } else {
            return false;
        }
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
        if (!empty($param)) {
            if (!empty($prefix)) {
                if (!isset($_SESSION[$prefix])) {
                    $_SESSION[$prefix] = array();
                }
                $_SESSION[$prefix][$param] = $value;
            } else {
                $_SESSION[$param] = $value;
            }
        }
        return true;
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
        return !empty($prefix) ? isset($_SESSION[$prefix][$param]) : isset($_SESSION[$param]);
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