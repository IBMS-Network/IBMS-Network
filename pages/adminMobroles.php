<?php

namespace pages;

use classes\clsAdminMobilePermissions;
use classes\clsAdminMobileRoles;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;

/**
 * Class for admin entity adminRole. Set actions under the \entities\AclMobileRole.
 * @author Anatoly.Bogdanov
 *
 */
class adminMobroles extends clsAdminController
{

    /**
     * self object
     * @var adminMobroles
     */
    private static $instance = null;

    /**
     * Object of the clsAdminMobileRoles class
     * @var clsAdminMobileRoles
     */
    private $objMobRole = "";

    /**
     * Constructor of the class of controller.
     * Set entity name, get object of the mobile Role, set menu item and tab item active
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('mobrole', ADMIN_ENTITIES_BLOCK);
        $this->objMobRole = clsAdminMobileRoles::getInstance();
        $this->parser->is_mobrole_tab = true; //set active role tab in sub menu
        $this->parser->is_mobuser_menu = true; //set active Mobile in left menu
        $this->parser->page_path = $this->path = ADMIN_PATH . '/mobroles/';
    }

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminMobroles();
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
        $this->parser->roles = $this->objMobRole->getRolesList();
        return $this->parser->render('@main/pages/admin_mob_roles.html');
    }

    /**
     * add Role controller
     * @see engine\modules\admin\clsAdminController::actionAdd()
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
                $roleIsUpdated = $this->objMobRole->addRole($this->post['name'], $this->post['perm']);
                $name = addslashes(strip_tags($this->post['name']));
                if ($roleIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->path);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301('Location: ' . $this->path . 'add');
        }

        $vars = array('action' => 'add', 'action_text' => clsCommon::getMessage("adding", "AdminTexts"));
        $objPerm = clsAdminMobilePermissions::getInstance();
        $this->parser->perms = $objPerm->getPermissionsList();
        return $this->parser->render('@main/pages/admin_mob_roles_form.html', $vars);
    }

    /**
     * Edit the mobile Role controller
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
                $roleIsUpdated = $this->objMobRole->updateRole(
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
                    clsCommon::redirect301('Location: ' . $this->path);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301('Location: ' . $this->path . 'edit/?id=' . clsCommon::isInt($this->get['id']));
        }

        $vars = array('action' => 'update', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        $roleData = $this->objMobRole->getRoleById(clsCommon::isInt($this->get['id']));
        if ($roleData) {
            $perm_by_role = $roleData->getPermissionsInArray();
            $perms = clsAdminMobilePermissions::getInstance()->getPermissionsList();
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

        return $this->parser->render("@main/pages/admin_mob_roles_form.html", $vars);
    }

    /**
     * Delete the mobile Role controller
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
            $roleIsDeleted = $this->objMobRole->deleteRole($id);
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
        clsCommon::redirect301('Location: ' . $this->path);
    }

}