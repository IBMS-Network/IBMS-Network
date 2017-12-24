<?php

namespace pages;

use classes\clsAdminMobilePermissions;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;

class adminMobpermissions extends clsAdminController
{

    /**
     * self object
     * @var adminMobpermissions
     */
    private static $instance = null;

    /**
     * Object of the clsAdminMobilePermissions class
     * @var clsAdminMobilePermissions
     */
    private $objMobPerm = "";

    protected $entity;

    /**
     * Constructor of the class of controller.
     * Set entity name, get object of the mobile Role, set menu item and tab item active
     */
    public function __construct()
    {
        parent::__construct();
        $this->objMobPerm = clsAdminMobilePermissions::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('mobpermission', ADMIN_ENTITIES_BLOCK);
        $this->parser->is_mobperm_tab = true; //set active perm tab in sub menu
        $this->parser->is_mobuser_menu = true; //set active Mobile in left menu
        $this->parser->page_path = $this->path = ADMIN_PATH . '/mobpermissions/';
    }

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminMobpermissions();
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
        $this->parser->perms = $this->objMobPerm->getPermissionsList();
        return $this->parser->render('@main/pages/admin_mob_permissions.html');
    }

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
                $permIsUpdated = $this->objMobPerm->addPermission($this->post['name']);
                $name = addslashes(strip_tags($this->post['name']));
                if ($permIsUpdated) {
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
        return $this->parser->render('@main/pages/admin_mob_permissions_form.html', $vars);
    }

    public function actionEdit()
    {

        $error = '';
        if (!empty($this->post['act']) && $this->post['act'] == "edit") {

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
                $permIsUpdated = $this->objMobPerm->updatePermission($this->post['id'], $this->post['name']);
                $name = addslashes(strip_tags($this->post['name']));
                if ($permIsUpdated) {
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

        $permData = $this->objMobPerm->getPermissionById(clsCommon::isInt($this->get['id']));
        $vars = array('action' => 'edit', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        if (!empty($permData)) { // if we have some element
            $vars['id'] = $permData->getId();
            $vars['perm_name'] = $permData->getName();
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/admin_mob_permissions_form.html", $vars);
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
            $permIsDeleted = $this->objMobPerm->deletePermission($id);
            if ($permIsDeleted) {
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