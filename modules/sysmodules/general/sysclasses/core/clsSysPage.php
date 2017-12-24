<?php

namespace engine\modules\general;

use engine\clsSysContent;
use engine\clsSysCommon;
use engine\clsSysScripts;
use engine\clsSysStyles;
use engine\modules\catalog\clsProducts;

abstract class clsSysPage extends clsSysContent
{

    /**
     * Scripts manager
     * @var clsSysScripts
     */
    protected $scriptsManager = null;

    /**
     * Styles manager
     * @var clsSysStyles
     */
    protected $stylesManager = null;
    protected $title = "";
    protected $blocks = array();
    protected $search_title = "";
    protected $meta_config = array();
    protected $meta_data = array();
    protected $url_alias = "";
    protected $url_method = "";
    protected $url = "";

    protected function __construct()
    {
        parent::__construct();
        $this->scriptsManager = new clsSysScripts;
        $this->stylesManager = new clsSysStyles;
        $this->getCommonClientHeader();
    }

    protected function setBreadCrumb($title)
    {
        $this->search_title = $title;
    }

    protected function getBreadCrumb()
    {
        return $this->search_title;
    }

    public function setUrlAlias($url_alias, $url_method = false)
    {
        $this->url = $this->url_alias = $url_alias;
        if ($url_method) {
            $this->url_method = $url_method;
            $this->url .= "#" . $url_method;
        }
    }

    public function isDisallowedPage()
    {
        $handle = @fopen(SYS_DOMAIN_PATH . "confsystem/robots.txt", "r");
        if ($handle) {
            while (!feof($handle)) {
                $string = fgets($handle, 4096);
                if (preg_match("|^Disallow:\s//*(\w+)//*$|smi", trim($string), $matches)) {
                    $page = $matches[1];
                    if (strtolower($page) == strtolower($this->config["URL"][$this->url]))
                        return true;
                }
                ;
            }
            fclose($handle);
        }
        return false;
    }

    /**
     * getCommonClientHeader function get defult settings for header template(include default js,css,meta data)
     *
     */
    protected function getCommonClientHeader()
    {
        $sys_ini_info = array('path' => CONF_SYS_PATH, 'name' => 'header.ini');
        $project_ini_info = $sys_ini_info;
        if (defined('CONFIG_DOMAIN_PATH')) {
            $project_ini_info['path'] = CONFIG_DOMAIN_PATH;
        }
        $header = clsSysCommon::getCommonIniFiles($sys_ini_info, $project_ini_info, false);
        if (!defined('CONFIG_DOMAIN_PATH')) {
            $search = array('{__class_path__}', '{__class_name__}');
            $repl = array($project_ini_info['path'], $project_ini_info['name']);
            $err_mes = clsSysCommon::getMessage('empty_file_path_in_load', 'Errors', $search, $repl);
            clsSysCommon::debugMessage($err_mes);
        }

        $this->scriptsManager->registerFile($header["Header_JS"]);
        $this->stylesManager->registerFile($header["Header_CSS"]);

        $sys_ini_info = array('path' => CONF_SYS_PATH, 'name' => 'meta.ini');
        $project_ini_info = $sys_ini_info;
        if (defined('CONFIG_DOMAIN_PATH')) {
            $project_ini_info['path'] = CONFIG_DOMAIN_PATH;
        }
        $this->meta_config = clsSysCommon::getCommonIniFiles($sys_ini_info, $project_ini_info, false);
        if (!defined('CONFIG_DOMAIN_PATH')) {
            $search = array('{__class_path__}', '{__class_name__}');
            $repl = array($project_ini_info['path'], $project_ini_info['name']);
            $err_mes = clsSysCommon::getMessage('empty_file_path_in_load', 'Errors', $search, $repl);
            clsSysCommon::debugMessage($err_mes);
        }
    }

    protected function getMetaData($type, $method = "")
    {
        $result = "";
        $name = get_class($this);
        $array = array();

        if (!empty($method) && !empty($this->meta_config[$name . "#" . $method]) && is_array($this->meta_config[$name . "#" . $method]) && count($this->meta_config[$name . "#" . $method]) > 0) {
            $array = $this->meta_config[$name . "#" . $method];
        } elseif (!empty($this->meta_config[$name]) && is_array($this->meta_config[$name]) && count($this->meta_config[$name]) > 0) {
            $array = $this->meta_config[$name];
        } elseif (!empty($this->meta_config["Default"]) && is_array($this->meta_config["Default"]) && count($this->meta_config["Default"]) > 0) {
            $array = $this->meta_config["Default"];
        }

        $vars = $this->meta_data;
        if (!empty($array)) {
            $vars["{DOMAIN}"] = ucfirst(SERVER_NAME);
            $result = str_replace(array_keys($vars), array_values($vars), $array[$type]);
        }
        return $result;
    }

    /**
     * Prepare system errors|messages text for template. Must be logic in templates to show errors.
     */
    protected function setSystemErrors(){
        // errors
        if($this->error->isErrors()){
            $errors = $this->error->getError();
            array_walk($errors, function(&$value){$value = array('text' => $value, 'class' => 'danger');});
            $this->parser->sys_errors = $errors;
            $this->parser->is_sys_errors = true;
        }else{
            $this->parser->is_sys_errors = false;
        }

        // messages
        if($this->error->isMessages()){
            $errors = $this->error->getError(true);
            array_walk($errors, function(&$value){$value = array('text' => $value, 'class' => 'success');});
            $this->parser->sys_messages = $errors;
            $this->parser->is_sys_messages = true;
        }else{
            $this->parser->is_sys_messages = false;
        }

        $this->error->clearError(true);
    }

    protected function setHeaderData($array = array())
    {
        if (!empty($array) && is_array($array) && count($array) > 0) {
            $this->meta_data = $array;
        }
    }

    /**
     * Function to get html of this page
     * This function declared in parent class clsContent. Required for site engine.
     *
     * @return html of current page
     */
    public function showContent()
    {
        $this->preparePage();
        /**
         * @todo do code refactoring
         */
        if (clsSysCommon::isAjax()) {
            $content = $this->getContent();
        } else {
            $content = $this->getContent();
        }
        return $content;
    }

    protected function preparePage()
    {
        $this->parser->scripts = $this->scriptsManager->getHTML();
        $this->parser->styles = $this->stylesManager->getHTML();
        
        $this->setSystemErrors();
    }

}
