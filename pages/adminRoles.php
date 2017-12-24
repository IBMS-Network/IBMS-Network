<?php

namespace pages;

use classes\clsAdminPermissions;
use classes\clsAdminRoles;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;

/**
 * Class for admin entity adminRole. Set actions under the Admin Roles.
 * @author Anatoly.Bogdanov
 *
 */
class adminRoles extends clsAdminController
{

    /**
     * self object
     *
     * @var adminRoles $instance
     */
    private static $instance = null;

    /**
     * Object of the clsAdminRoles class
     *
     * @var clsAdminRoles $objRole
     */
    private $objRole = "";

    /**
     * Constructor of the class of controller.
     * Set entity name, get object of the Role, set menu item and tab item active
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('role', ADMIN_ENTITIES_BLOCK);
        $this->objRole = clsAdminRoles::getInstance();
        $this->parser->is_role_tab = true; //set active role tab in sub menu
        $this->parser->is_admin_menu = true; //set active Administrators in left menu
        $this->parser->current_page = ADMIN_PATH . '/roles/'; // url path to current page
    }

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminRoles();
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
        $sort = !empty($this->get['sort']) ? $this->get['sort'] : '';
        $sorter = !empty($this->get['sorter'])  ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $this->parser->roles = $this->objRole->getRolesList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objRole->getRolesListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        return $this->parser->render('@main/pages/admins/roles/admin_roles.html');
    }

    /**
     * add Role controller
     * @see engine\modules\admin.clsAdminController::actionAdd()
     */
    public function actionAdd()
    {

        if (!empty($this->post['act']) && $this->post['act'] == "add") {
            $actionStatus = "";
            $error = '';
            if (empty($this->post['name'])) {
                $fieldname = clsAdminCommon::getAdminMessage('name', ADMIN_FIELDS_BLOCK);
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => $fieldname)
                );
                $this->error->setError($error, 1, false, true);
            }

            if (empty($error)) {
                $roleIsUpdated = $this->objRole->addRole($this->post['name'], $this->post['perm']);
                $name = addslashes(strip_tags($this->post['name']));
                if ($roleIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->parser->current_page);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301('Location: ' . $this->parser->current_page . 'add');
        }

        $vars = array('action' => 'add', 'action_text' => clsCommon::getMessage("adding", "AdminTexts"));
        $objPerm = clsAdminPermissions::getInstance();
        $this->parser->perms = $objPerm->getPermissionsList(1,1000);
        return $this->parser->render('@main/pages/admins/roles/admin_roles_form.html', $vars);
    }

    /**
     * Edit the Role controller
     * @see engine\modules\admin.clsAdminController::actionEdit()
     */
    public function actionEdit()
    {
        $error = '';
        if (!empty($this->post['act']) && $this->post['act'] == "update") {

            if (empty($this->post['name'])) {
                $fieldname = clsAdminCommon::getAdminMessage('name', ADMIN_FIELDS_BLOCK);
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => $fieldname)
                );
                $this->error->setError($error, 1, false, true);
            }

            if (empty($error)) {

                $roleIsUpdated = $this->objRole->updateRole(
                    $this->post['id'],
                    $this->post['name'],
                    $this->post['perm']
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($roleIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->parser->current_page);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301(
                'Location: ' . $this->parser->current_page . 'edit/?id=' . clsCommon::isInt($this->get['id'])
            );
        }

        $vars = array('action' => 'update', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        $roleData = $this->objRole->getRoleById(clsCommon::isInt($this->get['id']));
        if ($roleData) {
            $perm_by_role = $roleData->getPermissionsInArray();
            $perms = clsAdminPermissions::getInstance()->getPermissionsList(1,1000);
            $_perms = array();
            foreach ($perms as $k => $perm) {
                $_perms[$k]['name'] = $perm->getName();
                $_perms[$k]['id'] = $perm->getId();
                $_perms[$k]['checked'] = array_key_exists($perm->getId(), $perm_by_role) !== false ? 'checked' : '';
            }
            $this->parser->perms = $_perms;
            $role = array_pop($roleData);
            $vars['id'] = $roleData->getId();
            $vars['role_name'] = $roleData->getName();
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/admins/roles/admin_roles_form.html", $vars);
    }

    /**
     * Delete the Role controller
     * @see engine\modules\admin.clsAdminController::actionDelete()
     */
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
            $roleIsDeleted = $this->objRole->deleteRole($id);
            if ($roleIsDeleted) {
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