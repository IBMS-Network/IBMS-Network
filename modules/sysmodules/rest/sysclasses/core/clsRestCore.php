<?php

namespace engine\modules\rest;

use engine\modules\mobile\clsMobCore;
use engine\modules\mobile\clsMobController;
use engine\clsSysCommon;

class clsRestCore extends clsMobCore
{

    protected $itemId = NULL;

    /**
     * Constructor of clsMobCore class
     */
    function __construct()
    {
        parent::__construct();
        $this->config = clsSysCommon::getDomainConfig();
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
        $parts_url = parse_url(strtolower(trim($_SERVER['REQUEST_URI'], '/')));
        // @TODO: fix
        $parts_url_array = explode('/', $parts_url['path']);
        list($this->controllerName, $this->itemId) = $parts_url_array;

        // parse method
        $this->requestMethod = strtolower($_SERVER['REQUEST_METHOD']);

        switch ($this->requestMethod) {
            case 'get':
                // default actions for GET
                if ($this->controllerName == 'login' || $this->controllerName == 'logout') {
                    $this->actionName = $this->controllerName;
                    $this->controllerName = 'users';
                } elseif (is_null($this->itemId)) {
                    $this->actionName = 'index';
                } else {
                    $this->actionName = 'view';
                }
                break;
            case 'post':
                // default action for POST
                $this->actionName = 'add';
                break;
            case 'put':
                // default action for PUT
                $this->actionName = 'edit';
                break;
            case 'delete':
                // default action for DELETE
                $this->actionName = 'delete';
                break;
        }

        if (!$this->controllerName) {
            $this->controllerName = 'main';
        }
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
                if ($this->itemId) {
                    $this->controller->setParams(array('id' => $this->itemId));
                }
                $this->controller->setRequestParams($this->requestMethod);

                $return = true;
            } else {
                $this->controller->httpStatusCode = HTTP_STATUS_METHOD_NOT_ALLOWED;
//                throw new \Exception('Action is invalid.');
            }
        } else {
            $this->controller = new clsMobController();
            $this->controller->httpStatusCode = HTTP_STATUS_NOT_FOUND;
//            throw new \Exception('Controller class is invalid.');
        }

        return $return;
    }

}