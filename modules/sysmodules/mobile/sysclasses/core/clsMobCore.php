<?php

namespace engine\modules\mobile;

use engine\clsSysStorage;

use engine\clsSysCore;
//use engine\clsSysCommon;
//use engine\clsSysSession;
use engine\clsSysAcl;
use classes\clsMobAuthorisation;
use engine\clsMobAcl;
use clsSysHttpStatus;

class clsMobCore extends clsSysCore
{

    protected $controller = NULL;
    protected $action = NULL;
    protected $controllerName = NULL;
    protected $actionName = NULL;

    /**
     * Constructor of clsMobCore class
     */
    function __construct()
    {
        parent::__construct();

        // add mobile config
        require_once (MODULE_MOBILE_CONFIG_PATH . 'config.php');
        $this->config = clsMobCommon::getDomainConfig();
    }

    /**
     * Public method to start application
     */
    public function runApp()
    {
        print $this->showPage();
    }

    /**
     * Method for generated & output data
     */
    protected function showPage()
    {
        $this->initAction();
        if ($this->controller && $this->action){
            // check user permission for access on content
            $is_required = (defined('MOBILE_LOGIN_REQUIRED') && constant('MOBILE_LOGIN_REQUIRED') == 1);
            $is_required = !($this->controllerName == 'users' && ($this->actionName == 'login' || $this->actionName == 'logout')) && $is_required;
            if($is_required) {
                $token = $this->controller->getRequestParam('token');
                if (!empty($token)) {
                    $user = clsMobAuthorisation::getInstance()->loginByToken($token); // get user info by token
                    if ($user) { // we have found user by token
                        $permission = clsSysAcl::getInstance()->CheckAdminPermissions($this->controllerName, $this->actionName, $user);
                    } else {
                        $permission = false;
                    }
                }

            }else{
                $permission = true;
            }

            // if allow access - execute action
            if ($permission) {
                $controller = $this->controller;
                $action = $this->action;
                $controller->$action();
            }else{
                $this->controller->httpStatusCode = HTTP_STATUS_UNAUTHORIZED;
            }
            $content = $this->controller->showContent();
        }else{
            $this->controller->httpStatusCode = HTTP_STATUS_NOT_FOUND;
            $content = $this->controller->showContent();
        }

        // response header
        $this->controller->httpStatusResponse();

        // print output content
        print $content;
    }

    /**
     * Check & init action for controller
     *
     * @throws Exception
     *
     * @return boolean
     */
    protected function initAction()
    {
        $return = false;

        // parse request URI
        $parts_url = parse_url(trim($_SERVER['REQUEST_URI'], '/'));
        // @TODO: fix
        $parts_url_array = explode('/', trim($parts_url['path'], '/'));
        if ($parts_url_array[0] == 'mobile') {
            array_shift($parts_url_array);
        }
        list($this->controllerName, $this->actionName) = $parts_url_array;

        if (!$this->actionName) {
            $this->actionName = 'index';
        }

        // get, check & requre class
        $className = sprintf('mob%s', ucfirst($this->controllerName));

        $className = 'pages\\' . $className;
        if (class_exists($className)) {

            //create a instance of the controller
            $this->controller = new $className();

            //check if the action exists in the controller. if not, throw an exception.
            $actionName = sprintf('action%s', ucfirst($this->actionName));
            if (method_exists($this->controller, $actionName) !== false) {
                $this->action = $actionName;

                // set request params
                parse_str($parts_url['query'], $innerQuery);
                $this->controller->setParams($_GET, $_POST, $_REQUEST, $innerQuery);
                $return = true;
            } else {
                $this->controller->httpStatusCode = HTTP_STATUS_METHOD_NOT_ALLOWED;
            }
        } else {
            $this->controller = new clsMobController();
            $this->controller->httpStatusCode = HTTP_STATUS_NOT_FOUND;
        }

        return $return;
    }
}