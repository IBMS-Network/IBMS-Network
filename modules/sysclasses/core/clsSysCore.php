<?php

namespace engine;

/**
 * This class prepare data for application start(explode url, set data from incoming to inner
 * properties and object), include inner router logic(compare external route and inner route),
 * call inner Viewer class
 * @author Anatoly.Bogdanov
 *
 */
class clsSysCore
{

    /**
     * some inner property, which contain alias router path for incoming url
     * @var string
     */
    protected $url_alias = "";

    /**
     * some inner property, which contain method name to call for current router path
     * @var string
     */
    protected $url_method = "";

    /**
     * Array of $_GET methods data from url, like '?var1=val1&var2=val2' from url
     * @var array
     */
    protected $inner_get = array();

    /**
     * Inner object, with contain project or engine Common class
     * @var clsCommon|clsSysCommon
     */
    private $objCommon = NULL;

    /**
     * Assoc array of configuration data from project or default from engine
     * @var array
     */
    protected $config = array();

    /**
     * Constructor of clsSysCore class
     */
    function __construct()
    {
        $this->getCommonObject();
        $this->setProjectConfig();
    }

    /**
     * Parse incomming url, check it with router ini file, init GET method to
     * inner property $this->get
     *
     */
    protected function deCompileHref()
    {

        $parts_array = array();

        $parts_url = parse_url($_SERVER['REQUEST_URI']);

        // set url params
        $parts_array = explode("/", trim($parts_url["path"], "/"));

        if (!empty($parts_url["query"])) {
            // set get data to inner get array
            parse_str($parts_url["query"], $this->inner_get);
        }


        foreach ($this->config["URL"] as $key => $value) {
            $this->config["URL"][$key] = strtolower($value);
        }

        if (SITE_URI_ENABLED) {
            array_shift($parts_array);
        }

        foreach ($parts_array as $key => $value) {
            $value = trim(urldecode($value));
            $parts_array[$key] = $value;
            if (array_search(strtolower($value), $this->config["URL"]) !== false) {
                $this->url_alias = array_search(strtolower($value), $this->config["URL"]);
                if (preg_match("(.*)#([a-zA-Z]+)", $this->url_alias, $reg)) {
                    if (!empty($reg[2])) {
                        $this->url_alias = $reg[1];
                        $this->url_method = $reg[2];
                    }
                }
            }
        }

        $debug_mess = '';
        if (empty($this->url_alias)) {
            $is_rel_path = SITE_URI_ENABLED && $parts_url["path"] == "/" . trim(HTTP_REL_PATH, "/") . "/";
            if ($parts_url["path"] == "/" || $is_rel_path)
                $this->url_alias = "Main";
            else {
                if (clsSysCommon::getCommonDebug()) {
                    $debug_mess = "Uri part is not defined correctly." . "Part : [" . $parts_url["path"] . "]<br>" . "Site is not domain : [" . SITE_URI_ENABLED . "]<br>" . "HTTP_REL_PATH : [" . HTTP_REL_PATH . "]<br>";
                    $this->setClsCommonDebugMessage($debug_mess, __CLASS__ . '::' . __METHOD__, false, true);
                } else {
                    $this->setClsCommonRedirect404();
                }
            }
        }

        if (!empty($this->url_alias)) {
            $href = '';
            if ($this->url_method) {
                if (!empty($this->config[$this->url_alias]["href_" . $this->url_method])) {
                    $href = $this->config[$this->url_alias]["href_" . $this->url_method];
                }
            } else {
                if (!empty($this->config[$this->url_alias]["href"])) {
                    $href = $this->config[$this->url_alias]["href"];
                }
            }
            $url_array = explode("/", trim($href, "/"));
            foreach ($url_array as $key => $value) {
                $reg = array();
                if (preg_match("/(.*){([A-Z0-9_-]*)}(.*)/", $value, $reg)) {
                    if (!empty($parts_array[$key])) {
                        if ($reg[3])
                            $parts_array[$key] = substr($parts_array[$key], 0, -1 * strlen($reg[3]));
                        if ($reg[1])
                            $parts_array[$key] = substr($parts_array[$key], strlen($reg[1]));
                        $this->params[strtolower($reg[2])] = $parts_array[$key];
                    }else
                        $this->params[strtolower($reg[2])] = "";
                }
            }
        }

        return;
    }

    /**
     * Destructor of clsSysCore class
     */
    function __destructor()
    {

    }

    /**
     * Public method to start application
     */
    public function runApp()
    {
        echo $this->showPage();
    }

    /**
     * Prepare and parsing incoming data before start application
     * @return string
     */
    protected function getPageInfo()
    {
        $this->deCompileHref();
        return $this->url_alias;
    }

    /**
     * Enter description here ...
     * @return boolean|Ambiguous
     */
    private function showPage()
    {

        $pageName = $this->getPageInfo();
        if (!SITE_ENABLED && $this->config['URL']['Closed'] != $pageName) {
            $pageName = $this->config['URL']['Closed'];
        }

        $debug_mess = '';
        $throw_mess = 'unknown error';
        $is_page_name_set = true;
        if (empty($pageName) && clsSysCommon::getCommonDebug()) {
            if (clsSysCommon::getCommonDebug()) {
                $debug_mess = "<br />Page name is incorrect: [" . $pageName . "]<br />";
                $this->setClsCommonDebugMessage($debug_mess, __CLASS__ . '::' . __METHOD__, false, true);
                $is_page_name_set = false;
            } else {
                $this->setClsCommonRedirect404();
            }
        }

        if (file_exists(PAGE_PATH . $pageName . ".php") && $is_page_name_set) {

            if (class_exists('pages\\' . $pageName)) {
                $pageName = 'pages\\' . $pageName;

                $objPage = new $pageName();

                if (!empty($this->params)) {
                    $this->params = array_merge($this->params, $_GET);
                } else {
                    $this->params = $_GET;
                }
                // if we had $_GET data before .htaccess rewrite we put it in $this->params
                $this->params = array_merge($this->inner_get, $this->params);

                $objPage->setParams($this->params, $_POST, $_REQUEST);

                if (method_exists($objPage, $this->url_method)) {
                    $method = $this->url_method;
                    $content = $objPage->$method();
                } else {
                    $content = $objPage->showContent();
                }

                return $content;
            } elseif (clsSysCommon::getCommonDebug()) {
                $debug_mess = "<br />Page class[" . $pageName . "] is not a class<br />";
                $this->setClsCommonDebugMessage($debug_mess, __CLASS__ . '::' . __METHOD__, false, true);
            } else {
                $location = '/';
                if (defined('SERVER_URL_NAME') && defined('PageNotExists')) {
                    $location = "Location: " . SERVER_URL_NAME . "?err=" . PageNotExists;
                    $this->setClsCommonRedirect301($location);
                } else {
                    if (clsSysCommon::getCommonDebug()) {
                        $debug_mess = "<br />CONSTANTS [SERVER_URL_NAME] or [PageNotExists]" . " is not defined in project conficuration<br />";
                        $this->setClsCommonDebugMessage($debug_mess, __CLASS__ . '::' . __METHOD__, false, true);
                    } else {
                        $throw_mess = '<br />Cannot redirect.<br />';
                        throw new \Exception($throw_mess);
                    }
                }
            }
        } elseif (clsSysCommon::getCommonDebug()) {
            $debug_mess = "<br />Page class[" . $pageName . "] file not find<br />";
            $this->setClsCommonDebugMessage($debug_mess, __CLASS__ . '::' . __METHOD__, false, true);
        } else {
            $location = '/';
            if (defined('SERVER_URL_NAME') && defined('PageNotExists')) {
                $location = "Location: " . SERVER_URL_NAME . "?err=" . PageNotExists;
                $this->setClsCommonRedirect301($location);
            } else {
                if (clsSysCommon::getCommonDebug()) {
                    $debug_mess = "<br />CONSTANTS [SERVER_URL_NAME] or [PageNotExists]" . " is not defined in project conficuration<br />";
                    $this->setClsCommonDebugMessage($debug_mess, __CLASS__ . '::' . __METHOD__, false, true);
                } else {
                    $throw_mess = '<br />Cannot redirect.<br />';
                    throw new \Exception($throw_mess);
                }
            }
        }
        return false;
    }

    /**
     * Set name to inner property the project clsCommon class or engine clsSysCommon class
     * @return boolean
     */
    private function getCommonObject()
    {
        if (clsSysCommon::isProjectOn()) {
            $this->objCommon = 'clsCommon';
        } else {
            $this->objCommon = 'clsSysCommon';
        }
        return true;
    }

    /**
     * Set inner config array
     * @return boolean
     */
    protected function setProjectConfig()
    {
        $this->config = clsSysCommon::getDomainConfig();
        return true;
    }

    /**
     * Set debug message throw clsCommon or clsSysCommon class
     * @param string $message text of the debug message
     * @param string $title title for the debug block message. default empty.
     * @param int $use_debug_mode use engine constant USE_SYS_DEBUG or not. default depend on USE_SYS_DEBUG
     * @param bool $is_page
     * @return bool
     */
    protected function setClsCommonDebugMessage($message, $title = "", $use_debug_mode = USE_SYS_DEBUG, $is_page = false)
    {
        $result = false;
        if (method_exists($this->objCommon, 'debugMessage')) {
            $is_call = call_user_func(array($this->objCommon, 'debugMessage'), $message, $title, $use_debug_mode, $is_page);
            $result = !$is_call ? false : true;
        } else {
            clsSysCommon::debugMessage($message, $title, $use_debug_mode, $is_page);
            if (clsSysCommon::getCommonDebug()) {
                echo '<br>Cannot call clsCommon method [debugMessage] in ' . __CLASS__ . '<br>';
                $def_message = '<br>Cannot call clsCommon method [getDomainConfig] in ' . __CLASS__ . '<br>';
                clsSysCommon::debugMessage($def_message, __CLASS__ . '::' . __METHOD__, false, true);
            }
        }
        return $result;
    }

    /**
     * Set 404 redirect from clsCommon or clsSysCommon class
     * @return boolean
     */
    protected function setClsCommonRedirect404()
    {
        $result = false;
        if (method_exists($this->objCommon, 'redirect404')) {
            $is_call = true;
            $is_call = call_user_func(array($this->objCommon, 'redirect404'));
            $result = !$is_call ? false : true;
        } else {
            if (clsSysCommon::getCommonDebug()) {
                $def_message = '<br>Cannot call clsCommon method [redirect404] in ' . __CLASS__ . '<br>';
                $this->setClsCommonDebugMessage($def_message, __CLASS__ . '::' . __METHOD__, false, true);
            }
        }
        return $result;
    }

    /**
     * Set 301 redirect to incoming page from clsCommon or clsSysCommon class
     * @param string $location
     * url adress to page to redirect
     * @return boolean
     */
    protected function setClsCommonRedirect301($location = '/')
    {
        $result = false;
        if (method_exists($this->objCommon, 'redirect301')) {
            $is_call = true;
            $is_call = call_user_func(array($this->objCommon, 'redirect301'), $location);
            $result = !$is_call ? false : true;
        } else {
            if (clsSysCommon::getCommonDebug()) {
                $def_message = '<br>Cannot call clsCommon method [redirect301] in ' . __CLASS__ . '<br>';
                $this->setClsCommonDebugMessage($def_message, __CLASS__ . '::' . __METHOD__, false, true);
            }
        }
        return $result;
    }

}