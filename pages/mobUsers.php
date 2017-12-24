<?php

namespace pages;

use classes\clsMobAuthorisation;
use classes\clsUser;
use engine\modules\mobile\clsMobController;

class mobUsers extends clsMobController
{

    /**
     * Inner variable to hold own object of a class
     * @var object $instance - object of the mobUsers
     */
    private static $instance = null;

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new mobUsers();
        }
        return self::$instance;
    }

    /**
     * Get list user
     *
     * @return array
     */
    public function actionIndex()
    {
        $return = array();

        $count = $this->_isRequestInt('count');
        $offset = $this->_isRequestInt('offset', false);

        if (!$this->error->isErrors()) {
            $user = clsUser::getInstance();
            $return = $user->getList($count, $offset);
        }

        $this->output['result'] = $return;
        return $return;
    }

    /**
     * Add new item in DB by request with data
     *
     * @return int
     */
    public function actionAdd()
    {
        $return = array('status' => 0);

        // preapare data
        $name = $this->_isRequestString('name');
        $surname = $this->_isRequestString('surname');
        $phone = $this->_isRequestString('phone', true, 'phone');
        $email = $this->_isRequestString('email', true, 'email');
        $password = $this->_isRequestString('password', true, 'password');
        $address = $this->_isRequestString('address', false);
        $fullName = $this->_isRequestString('fullName', false);

        // insert data in DB
        if (!$this->error->isErrors()) {
            $user = clsUser::getInstance();
            $return = $user->createUser($name, $surname, $phone, $email, $password, $address, $fullName);
            if ($return > 0) {
                $this->httpStatusCode = HTTP_STATUS_CREATED;
                $return['id'] = (int)$return;
            } else {
                $this->httpStatusCode = HTTP_STATUS_BAD_REQUEST;
            }
        }

        $this->output['result'] = $return;
        return $return;
    }

    /**
     * View data by id in request
     *
     * @return array
     */
    public function actionView()
    {
        $return = array();
        $itemId = $this->_isRequestId();


        if (!empty($itemId)) {
            $user = clsUser::getInstance();
            $return = $user->getUserById($itemId);
            // clear output: unset access data
            if ($return) {
                unset($return['password']);
                unset($return['token']);
            }
        }

        $this->output['result'] = $return;
        return $return;
    }

    /**
     * Edit data item by request
     *
     * @return int
     */
    public function actionEdit()
    {
        $return = 0;

        $itemId = $this->_isRequestId();
        if (!empty($itemId)) {
            // preapare data
            $data = array('id' => $itemId);
            if ($name = $this->_isRequestString('name', false)) {
                $data['first_name'] = $name;
            }
            if ($surname = $this->_isRequestString('surname', false)) {
                $data['last_name'] = $surname;
            }
            if ($phone = $this->_isRequestString('phone', false, 'phone')) {
                $data['phone'] = $phone;
            }
            if ($email = $this->_isRequestString('email', false, 'email')) {
                $data['email'] = $email;
            }
            if ($password = $this->_isRequestString('password', false, 'password')) {
                $data['password'] = $password;
            }
            if ($address = $this->_isRequestString('address', false)) {
                $data['address'] = $address;
            }
            if ($fullName = $this->_isRequestString('fullName', false)) {
                $data['full_name'] = $fullName;
            }

            // update data in DB
            if (!$this->error->isErrors()) {
                $user = clsUser::getInstance();
                $return = $user->updateUser($data);

                if ($return) {
                    $this->httpStatusCode = HTTP_STATUS_OK;
                } else {
                    $this->httpStatusCode = HTTP_STATUS_NO_CONTENT;
                }
            }
        }

        $this->output['result'] = array('status' => (int)$return);
        return $return;
    }

    /**
     * Delete item by request
     *
     * @return int
     */
    public function actionDelete()
    {
        $return = 0;
        $itemId = $this->_isRequestId();

        if (!empty($itemId)) {
            $user = clsUser::getInstance();
            $return = $user->deleteUser($itemId);

            if ($return) {
                $this->httpStatusCode = HTTP_STATUS_OK;
            } else {
                $this->httpStatusCode = HTTP_STATUS_NO_CONTENT;
            }
        }

        $this->output['result'] = array('status' => (int)$return);
        return $return;
    }

    /**
     * Login user in project
     *
     * @return array
     */
    public function actionLogin()
    {
        $email = $this->_isRequestString('email', true, 'email');
        $password = $this->_isRequestString('password', true, 'password');

        if (!$this->error->isErrors()) {
            $objAuth = clsMobAuthorisation::getInstance();
            $return = $objAuth->login($email, $password);
            if (isset($return['result']) && !empty($return['result'])) {
                $this->output['result']['status'] = 1;

                $role = $this->session->GetParam('role_name', 'auth_user');
                $return['result']['role'] = $role;

//                $tokenAccess = $this->session->getParam('token', 'auth_user');
//                $this->output['result']['token'] = $tokenAccess;
                $this->output['result']['user_id'] = $return['result']['id'];
                $this->output['result']['token'] = $return['result']['token'];
            } else {
                $this->error->setError(sprintf('usser_account_no_valid'));
            }
        }

        return $this->output['result'];
    }

}