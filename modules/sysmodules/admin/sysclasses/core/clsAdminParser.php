<?php

namespace engine\modules\admin;

/**
 * This class is Template Engine.
 * It can be inherit by project class
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminParser extends clsSysParser
{

    /**
     * data from templte
     * @var text
     */
    protected $_tpl = "";

    /**
     * tpl file name, if template is from file
     * @var string
     */
    protected $tpl_name = "";

    /**
     * type of tpl (BLOCK / PAGE / EMAIL ...)
     * @var string
     */
    protected $tpl_type = "";

    /**
     * Array of placeholders from template
     * @var array
     */
    protected $_tpl_vars = array();

    /**
     * Array of replacements for placeholders
     * @var array
     */
    protected $tpl_vars = array();

    /**
     * path for cache
     * @var string
     */
    protected $path = "";

    public function __construct()
    {
        
    }

    public function __call($function, $args)
    {
        $result = false;
        $params = array();
        if (preg_match('|set([a-z]*)Template|i', $function, $params)) {

            // get varieables for function
            if (count($args) == 2) {
                list ( $tpl, $is_file ) = $args;
            } elseif (count($args) == 1) {
                list ( $tpl ) = $args;
                $is_file = true;
            } else {
                if (clsSysCommon::getCommonDebug()) {
                    $search = array('{__func_name__}');
                    $repl = array(__CLASS__ . '::' . $function);
                    $error_message = clsSysCommon::getMessage('call_undefined_func', 'Errors', $search, $repl);
                    throw new \Exception($error_message);
                } else {
                    $search = array('{__err_num__}');
                    $repl = array(3);
                    $replace = array('block' => 'Errors', 'search' => $search, 'repl' => $repl);
                    clsSysCommon::debugMessage("system_down", __METHOD__, false, false, $replace);
                }
            }

            $postfix = !empty($params [1]) ? (strtoupper($params [1]) . '_') : '';

            // set current type of using template
            $this->tpl_type = empty($postfix) ? 'PAGE' : strtoupper($postfix);

            $_result = false;
            $_result = $this->setMixedTemplate($tpl, $postfix, $is_file);
            if (!$_result) {
                // result from setTemplate is incorrect
            }
        } else { // strange call function
            if (clsSysCommon::getCommonDebug()) {
                $search = array('{__func_name__}');
                $repl = array(__CLASS__ . '::' . $function);
                $error_message = clsSysCommon::getMessage('call_undefined_func', 'Errors', $search, $repl);
                throw new \Exception($error_message);
            } else {
                $search = array('{__err_num__}');
                $repl = array(3);
                $replace = array('block' => 'Errors', 'search' => $search, 'repl' => $repl);
                clsSysCommon::debugMessage("system_down", __METHOD__, false, false, $replace);
            }
        }
    }

    /**
     * setMixedTemplate function set templates for replacement in Inner Object
     *
     * @param string $tpl
     * path to file of template or text of template
     * @param string $pref
     * prefix from call method for constant. setBlockTemplate - 'Block' is prefix and TPL_BLOCK_DOMAIN_PATH is constant
     * @param boolean $is_file
     * set incoming $tpl is path to file or $tpl is text of template. True - is file.
     * @throws Exception
     * @return boolean
     */
    public function setMixedTemplate($tpl, $pref = '', $is_file = true)
    {
        $result = false;
        $const = 'TPL_' . $pref . 'DOMAIN_PATH';
        $const_common = 'TPL_' . $pref . 'COMMON_PATH';

        // check if template will be from file or from incoming variable
        if ($is_file) { // if file
            // check tpl's variables
            if (clsSysCommon::isProjectOn() && (!defined($const) || !defined($const_common))) {
                $search = array('{__const_name__}');
                $repl = array($const . ' or ' . $const_common);
                $error_message = clsSysCommon::getMessage('const_undefined', 'Errors', $search, $repl);
                throw new \Exception($error_message);
            }

            // try to get file from project domain folder
            $this->tpl_name = $file = constant($const) . $tpl;
            $file_common = constant($const_common) . $tpl;
            $search = array('{__class_path__}', '{__class_name__}');
            if (!file_exists($file) && clsSysCommon::getCommonDebug()) { // file not exists on server
                $repl = array(constant($const), $tpl);
                $replace = array('block' => 'Errors', 'search' => $search, 'repl' => $repl);
                clsSysCommon::debugMessage("empty_file_path_in_load", __METHOD__, false, false, $replace);
            } elseif (file_exists($file)) {
                $this->_tpl = @file_get_contents($file);
                $result = true;
            }

            // if cannot find tpl in domain folder, try to find it in common directory
            if (!$result) {
                if (!file_exists($file_common)) { // try to get template from project document root folder
                    if (clsSysCommon::getCommonDebug()) {
                        $repl = array(constant($const_common), $tpl);
                        $replace = array('block' => 'Errors', 'search' => $search, 'repl' => $repl);
                        clsSysCommon::debugMessage("empty_file_path_in_load", __METHOD__, false, false, $replace);
                    } else {
                        $search = array('{__err_num__}');
                        $repl = array(2);
                        $error_message = clsSysCommon::getMessage('system_down', 'Errors', $search, $repl);
                        throw new \Exception($error_message);
                    }
                } else {
                    $this->_tpl = @file_get_contents($file_common);
                    $result = true;
                }
            }
        } else { // if incoming variable with template
            if (!empty($tpl)) {
                $this->_tpl = $tpl;
                $result = true;
            } else {
                
            }
        }
        return $result;
    }

    /**
     * Prepare placeholders from tpl
     * @return boolean
     */
    private function getVarFromTPL()
    {
        $result = false;
        if (!empty($this->_tpl)) {
            preg_match_all("/({[A-Z0-9_-]*})/", $this->_tpl, $result);
            $this->_tpl_vars = $result [0];
            $result = true;
        }
        return $result;
    }

    /**
     * Setter from script for replacement array of variables to template.
     * @param array $vars
     * assoc array of variables for replacement
     * @param boolean $use_tpl
     * use concat for {_PLACEHOLDER_} format or not. True - use additional concating.
     * @return boolean
     */
    public function setVars($vars, $use_tpl = false)
    {
        $result = false;
        // check incoming array
        if (!is_array($vars)) {
            if (clsSysCommon::getCommonDebug()) {
                $search = array('{__var_name__}', '{__req_type__}', '{__var_type__}');
                $repl = array('vars', 'array', gettype($vars));
                $replace = array('block' => 'Errors', 'search' => $search, 'repl' => $repl);
                clsSysCommon::debugMessage("data_bad_type", __METHOD__, false, false, $replace);
            }
        } else {

            foreach ($vars as $key => $value) {
                if (!$use_tpl) {
                    $this->setVar($key, $value);
                } else {
                    $this->setVar('{' . strtoupper($key) . '}', $value);
                }
            }
            $result = true;
        }
        return $result;
    }

    /**
     * Setter from script for replacement 1 variable to template.
     * @param string $key
     * placeholder name
     * @param mixed $var
     * value to replace
     * @param boolean $is_null
     * replace placeholder or not if value is empty. True - replace force.
     */
    public function setVar($key, $var, $is_null = false)
    {
        if ((!empty($var) && !empty($key)) || (!empty($key) && $is_null)) {
            $this->tpl_vars [$key] = $var;
        }
    }

    /**
     * Clear current object data
     */
    public function clear()
    {
        $this->__destruct();
    }

    public function getResult($write = false)
    {

        // if template is not set
        if (empty($this->_tpl)) {
            if (clsSysCommon::getCommonDebug()) {
                $replace = array('block' => 'Errors');
                clsSysCommon::debugMessage("tpl_undefined", __METHOD__, false, false, $replace);
            }
            return false;
        }
        $this->getVarFromTPL();

        if (count($this->_tpl_vars) < 1) {
            return $this->_tpl;
        }

        if (count($this->tpl_vars) < 1) {
            return str_replace($this->_tpl_vars, array(), $this->_tpl);
        }
        $result = array();

        foreach (array_values($this->_tpl_vars) as $value) {
            if (array_key_exists($value, $this->tpl_vars)) {
                $result [$value] = $this->tpl_vars [$value];
            } else {
                $result [$value] = "";
            }
        }
        $content = str_replace(array_keys($result), array_values($result), $this->_tpl);
        if (defined('USE_CACHE') && USE_CACHE) {
            if (empty($this->path)) {
                // debug this
            } elseif ($write == true) {
                clsSysCommon::CreateDirRec(dirname($this->path));
                $result = @file_put_contents($this->path, $content);
                if (useDebug && ($result < 1 || $result === false)) {
                    // debug this;
                } elseif ($result < 1 || $result === false) {
                    // debug here
                } else {
                    $this->path = "";
                    return $content;
                }
            }
        }
        return $content;
    }

    /**
     * get data from cache
     * @param string $keyword
     * @param boolean $isCache
     * use cache or not. TRUE - use cache.
     * @return string
     */
    public function useCache($keyword, $isCache = USE_CACHE)
    {
        $result = "";
        $args = func_get_args();

        if (defined('SYS_CACHE_DOMAIN_PATH')) {
            $path_to = SYS_CACHE_DOMAIN_PATH . $args [0] . "/" . join("_", $args) . ".html";
            if (file_exists($path_to) && $isCache) {
                $content = file_get_contents($path_to);
                if (strlen($content) > 0) {
                    $result = $content;
                }
            }
        } else {
            if (clsSysCommon::getCommonDebug()) {
                $search = array('{__const_name__}');
                $repl = array('SYS_CACHE_DOMAIN_PATH ');
                $error_message = clsSysCommon::getMessage('const_undefined', 'Errors', $search, $repl);
                throw new \Exception($error_message);
            }
        }

        $this->path = $path_to;
        return $result;
    }

    /**
     * Destructor of the class
     */
    public function __destruct()
    {
        $this->_tpl = NULL;
        $this->_tpl_vars = NULL;
        $this->tpl_vars = NULL;
        $this->tpl_name = NULL;
        $this->tpl_type = NULL;
        $this->path = NULL;
    }

}