<?php

namespace engine\session\adapter;

use Rediska;

/**
 * This class is Redis session storage adapter
 */
class clsRedisAdapter extends clsAbstractAdapter
{
    private $_rediska = null;
    private $_prefix = null;

    public function __construct()
    {
        $this->_rediska = new Rediska();
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
            return !empty($prefix) ? $this->_rediska->getFromHash($prefix, $param) : $this->_rediska->getHash($param);
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
     */
    public function clearParam($param, $prefix = "") {
        if (!empty($prefix)) {
            // need check ?
            if ($this->_rediska->existsInHash($prefix, $param)) {
                $this->_rediska->deleteFromHash($prefix, $param);
            }
        } else {
            $this->_rediska->delete($param);
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
            $fields = $this->_rediska->getHashFields($prefix);
            foreach ($fields As $field){
                $this->_rediska->deleteFromHash($prefix, $field);
            }
            return true;
        } else {
            return false;
        }
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
        if (!empty($sess_arr) && is_array($sess_arr) && count($sess_arr) > 0) {
            foreach ( $sess_arr as $key => $value ) {
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
                $this->_rediska->setToHash($prefix, $param, $value);
            } else {
                $this->_rediska->set($param, $value);
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
        return !empty($prefix) ? $this->_rediska->existsInHash($prefix, $param) : $this->_rediska->exists($param);
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
