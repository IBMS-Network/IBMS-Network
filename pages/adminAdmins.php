<?php

namespace pages;

use classes\clsAdmin;
use classes\clsAdminRoles;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;

class adminAdmins extends clsAdminController
{

    private static $instance = null;
    protected $entity;
    private $objAdmin = "";

    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('admin', ADMIN_ENTITIES_BLOCK);
        $this->objAdmin = clsAdmin::getInstance();
        $this->parser->is_admin_tab = true; //set active admin tab in sub menu
        $this->parser->is_admin_menu = true; //set active Administrators in left menu
        $this->parser->current_page = ADMIN_PATH . '/admins/'; // url path to current page
    }

    /**
     * getInstance function create or return already exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminAdmins();
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
        $page = (int)$this->get['page'] > 0 && (int)$this->get['page'] < 4294967295 ? (int)$this->get['page'] : 1;
        $sort = !empty($this->get['sort']) ? $this->get['sort'] : '';
        $sorter = !empty($this->get['sorter'])  ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $this->parser->adminers = $this->objAdmin->getAdminsList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objAdmin->getAdminsListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        $objRole = clsAdminRoles::getInstance();
        $this->parser->roles = $objRole->getRolesList();
        return $this->parser->render('@main/pages/admins/admins/admin_admins.html');
    }

    public function actionAdd()
    {
        if (!empty($this->post['act']) && $this->post['act'] == "add") {
            $error = '';
            if (empty($this->post['login'])) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Login')
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
                $adminIsUpdated = $this->objAdmin->addAdmin(
                    $this->post['login'],
                    $this->post['password'],
                    $this->post['role']
                );
                $login = addslashes(strip_tags($this->post['login']));
                if ($adminIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $login)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->parser->current_page);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $login)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301('Location: ' . $this->parser->current_page . 'add');
        }

        $objRole = clsAdminRoles::getInstance();
        $this->parser->roles = $objRole->getRolesList();
        $vars = array('action' => 'add', 'action_text' => clsCommon::getMessage("adding", "AdminTexts"));
        return $this->parser->render('@main/pages/admins/admins/admin_admins_form.html', $vars);
    }

    public function actionEdit()
    {
        $error = '';
        if (!empty($this->post['act']) && $this->post['act'] == "edit") {

            if (empty($this->post['login'])) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Login')
                );
                $this->error->setError($error, 1, false, true);
            }

            if (empty($error)) {
                $password = !empty($this->post['password']) ? $this->post['password'] : '';
                $adminIsUpdated = $this->objAdmin->updateAdmin(
                    $this->post['id'],
                    $this->post['login'],
                    $password,
                    $this->post['role']
                );
                $login = addslashes(strip_tags($this->post['login']));
                if ($adminIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $login)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->parser->current_page);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $login)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301(
                'Location: ' . $this->parser->current_page . 'edit/?id=' . clsCommon::isInt($this->get['id'])
            );
        }

        $adminData = $this->objAdmin->getAdminById(clsCommon::isInt($this->get['id']));
        $objRole = clsAdminRoles::getInstance();
        $roles = $objRole->getRolesList();
        $_roles = array();
        foreach ($roles as $k => $role) {
            if ($role->getId() == $adminData->getRole()->getId()) {
                $_roles[$k]['selected'] = 'selected';
            } else {
                $_roles[$k]['selected'] = '';
            }
            $_roles[$k]['name'] = $role->getName();
            $_roles[$k]['id'] = $role->getId();
        }
        $this->parser->roles = $_roles;
        $vars = array('action' => 'edit', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        if (!empty($adminData)) { // if we have some element
            $vars['id'] = $adminData->getId();
            $vars['login'] = $adminData->getLogin();
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/admins/admins/admin_admins_form.html", $vars);
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
            $adminIsDeleted = $this->objAdmin->deleteAdmin($id);
            if ($adminIsDeleted) {
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