<?php

namespace pages;

use classes\clsAdminUsers;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use entities\UserStatusTypes;
use entities\UserSexTypes;
use entities\User;

class adminUsers extends clsAdminController
{
    /**
     * self object
     * @var adminUsers $instance
     */
    private static $instance = null;
    /**
     * translation of entity
     * @var string $entity
     */
    protected $entity;
    /**
     * object of clsAdminUsers
     * @var clsAdminUsers $objUser
     */
    private $objUser = "";

    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('user', ADMIN_ENTITIES_BLOCK);
        $this->objUser = clsAdminUsers::getInstance();
        $this->parser->is_user_tab = true; //set active admin tab in sub menu
        $this->parser->is_users_menu = true; //set active Administrators in left menu
        $this->parser->current_page = ADMIN_PATH . '/users/'; // url path to current page
    }

    /**
     * getInstance function create or return already exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminUsers();
        }
        return self::$instance;
    }

    /**
     * Get start page
     *
     * @return array
     */
    public function actionIndex()
    {
        $page = (int)$this->get['page'] > 0 && (int)$this->get['page'] < INT_MAX ? (int)$this->get['page'] : 1;
        $sort = !empty($this->get['sort']) ? $this->get['sort'] : 'id';
        $sorter = !empty($this->get['sorter']) ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $this->parser->users = $this->objUser->getUsersList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $this->parser->statuses = UserStatusTypes::getValuesByAssoc();
        $count = $this->objUser->getUsersListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        return $this->parser->render('@main/pages/users/admin/admin_users.html');
    }

    public function actionAdd()
    {
        if (!empty($this->post['act']) && $this->post['act'] == "add") {
            $error = '';
            if (empty($this->post['email'])) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Email')
                );
                $this->error->setError($error, 1, false, true);
            }
            if (empty($this->post['password'])) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Password')
                );
                $this->error->setError($error, 1, false, true);
            }

            if (empty($error)) {
                $adminIsUpdated = $this->objUser->addUser(
                    $this->post['email'],
                    $this->post['password'],
                    $this->post['firstName'],
                    $this->post['lastName'],
                    $this->post['sex'],
                    $this->post['phone'],
                    $this->post['city'],
                    $this->post['status']
                );
                $email = addslashes(strip_tags($this->post['email']));
                if ($adminIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $email)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->parser->current_page);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $email)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301('Location: ' . $this->parser->current_page . 'add');
        }

        $this->parser->statuses = UserStatusTypes::getValuesByAssoc();
        $this->parser->sexes = UserSexTypes::getValuesByAssoc();
        $vars = array('action' => 'add', 'action_text' => clsCommon::getMessage("adding", "AdminTexts"));
        return $this->parser->render('@main/pages/users/admin/admin_users_form.html', $vars);
    }

    public function actionEdit()
    {
        $error = '';
        $id = clsCommon::isInt($this->get['id']);
        if (!empty($this->post['act']) && $this->post['act'] == "edit") {

            if (empty($this->post['email'])) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Email')
                );
                $this->error->setError($error, 1, false, true);
            }

            if (empty($error)) {
                $password = !empty($this->post['password']) ? $this->post['password'] : '';
                $userIsUpdated = $this->objUser->updateUser(
                    $this->post['id'],
                    $this->post['email'],
                    $password,
                    $this->post['firstName'],
                    $this->post['lastName'],
                    $this->post['sex'],
                    $this->post['phone'],
                    $this->post['city'],
                    $this->post['status']
                );
                $email = addslashes(strip_tags($this->post['email']));
                if ($userIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $email)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->parser->current_page);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $email)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301(
                'Location: ' . $this->parser->current_page . 'edit/?id=' . $id
            );
        }

        /** @var User $userData */
        $userData = $this->objUser->getUserById($id);
        $this->parser->statuses = UserStatusTypes::getValuesByAssoc();
        $this->parser->statuses_values = UserStatusTypes::getValues();
        $this->parser->statuses_k_values = array_keys(UserStatusTypes::getValues());
        $this->parser->sexes = UserSexTypes::getValuesByAssoc();
        $vars = array('action' => 'edit', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        if (!empty($userData) && $userData instanceof User) { // if we have some element
            $this->parser->user = $userData;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => $id)
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/users/admin/admin_users_form.html", $vars);
    }

    public function actionDelete()
    {
        $id = clsCommon::isInt($this->get['id']);
        if (empty($id)) {
            $error = clsAdminCommon::getAdminMessage(
                'error_field_empty',
                ADMIN_ERROR_BLOCK,
                array('{%fieldname}' => 'ID')
            );
            $this->error->setError($error, 1, false, true);
        }

        if (empty($error)) {
            $userIsDeleted = $this->objUser->deleteUser($id);
            if ($userIsDeleted) {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'succ_del_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $id)
                );
                $this->error->setError($actionStatus, 1, true, true);
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_del_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $id)
                );
                $this->error->setError($actionStatus, 1, false, true);
            }
        }
        clsCommon::redirect301('Location: ' . $this->parser->current_page);
    }

}