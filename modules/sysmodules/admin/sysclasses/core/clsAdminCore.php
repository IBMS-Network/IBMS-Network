<?php

namespace engine\modules\admin;

use classes\clsSession;
use engine\clsSysAcl;
use engine\clsSysCore;
use engine\clsSysCommon;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;

class clsAdminCore extends clsSysCore
{

    protected $controller = null;

    protected $action = null;

    protected $controllerName = null;

    protected $actionName = null;

    /**
     * Constructor of clsAdminCore class
     */
    public function __construct()
    {
        parent::__construct();
        // add admin config
        require_once(MODULE_ADMIN_CONFIG_PATH . 'config.php');
        $this->config = clsSysCommon::getDomainConfig();
    }

    /**
     * Public method to start application
     */
    public function runApp()
    {
        echo $this->showPage();
    }

    /**
     * Method for generated & output data
     */
    protected function showPage()
    {
        $this->initAction();
        $content = '';
        $user = clsSession::getInstance()->getParam('admin_user');
        $action = $this->action;

        if (empty($user)) {
            if ($this->controllerName != "login") {
                header('Location: ' . ADMIN_PATH . '/login/');
            } else {
                $content = $this->controller->showContent($action);
            }
        } else {
            // check admin permission for access on content
            $permission = clsSysAcl::getInstance()->CheckAdminPermissions(
                $this->controllerName,
                $this->actionName,
                $user
            );
            if ($this->controllerName == "logout" || $permission) {
                $content = $this->controller->showContent($action);
            } else {
                // set error on page that have no permission to visit this page
                $actionStatus = clsAdminCommon::getAdminMessage('error_not_permiss', ADMIN_ERROR_BLOCK);
                $this->controller->error->setError($actionStatus, 1, false, true);
                // missing permissions
                clsCommon::redirect301('Location: /admin/?err=now_permissions');
            }
        }

        print $content;
    }

    /**
     * Check & init action for controller
     * @return bool
     * @throws \Exception
     */
    protected function initAction()
    {
        $return = false;

        // parse request URI
        $parts_url = parse_url(trim($_SERVER['REQUEST_URI'], '/'));
        // @TODO: fix
        $parts_url_array = explode('/', trim($parts_url['path'], '/'));
        if ($parts_url_array[0] == 'admin') {
            array_shift($parts_url_array);
        }
        list($this->controllerName, $this->actionName) = $parts_url_array;

        if (!$this->controllerName) {
            $this->controllerName = 'main';
        }
        if (!$this->actionName) {
            $this->actionName = 'index';
        }

        // get, check & requre class
        $className = sprintf('admin%s', ucfirst($this->controllerName));

        $className = 'pages\\' . $className;
        if (class_exists($className)) {

            //create a instance of the controller
            $this->controller = new $className();

            //check if the action exists in the controller. if not, throw an exception.
            $actionName = sprintf('action%s', ucfirst($this->actionName));
            if (method_exists($this->controller, $actionName) !== false) {
                $this->action = $actionName;
                // set request params
                $this->controller->setParams($_GET, $_POST, $_REQUEST);
                $return = true;
            } else {
                throw new \Exception('Action is invalid.');
            }
        } else {
            throw new \Exception('Controller class is invalid.');
        }

        return $return;
    }

}