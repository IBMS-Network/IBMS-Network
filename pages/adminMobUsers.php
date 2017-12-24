<?php

namespace pages;

use classes\clsAdminMobileRoles;
use classes\clsAdminMobUsers;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;

class adminMobUsers extends clsAdminController
{

    private static $instance = null;

    private $objUser = "";

    protected $entity;

    private $path = ADMIN_PATH;

    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('mobuser', ADMIN_ENTITIES_BLOCK);
        $this->objUser = clsAdminMobUsers::getInstance();
        $this->parser->is_mobuser_tab = true; //set active mobusers tab in sub menu
        $this->parser->is_mobuser_menu = true; //set active Mobile in left menu
        $this->parser->page_path = $this->path = ADMIN_PATH . '/users/';
    }

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminMobUsers();
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
        $this->parser->users = $this->objUser->getMobUsersList();
        return $this->parser->render('@main/pages/admin_mob_users.html');
    }

    public function actionAdd()
    {
        if (!empty($this->post['act']) && $this->post['act'] == "add") {
            $actionStatus = "";
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
                $mobUserIsUpdated = $this->objUser->addMobUser(
                    $this->post['login'],
                    $this->post['password'],
                    $this->post['role']
                );
                $login = addslashes(strip_tags($this->post['login']));
                if ($mobUserIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $login)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->path);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $login)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301('Location: ' . $this->path . '/add');
        }

        $objMRole = clsAdminMobileRoles::getInstance();
        $this->parser->roles = $objMRole->getRolesList();
        $vars = array('action' => 'add', 'action_text' => clsCommon::getMessage("adding", "AdminTexts"));
        return $this->parser->render('@main/pages/admin_mob_users_form.html', $vars);
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
                $adminIsUpdated = $this->objUser->updateMobUser(
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
                    clsCommon::redirect301('Location: ' . $this->path);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $login)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301('Location: ' . $this->path . '/edit/?id=' . clsCommon::isInt($this->get['id']));
        }

        $mobuserData = $this->objUser->getMobUserById(clsCommon::isInt($this->get['id']));
        $objMRole = clsAdminMobileRoles::getInstance();
        $roles = $objMRole->getRolesList();
        $_roles = array();
        foreach ($roles as $k => $role) {
            if ($role->getId() == $mobuserData->getRole()->getId()) {
                $_roles[$k]['selected'] = 'selected';
            } else {
                $_roles[$k]['selected'] = '';
            }
            $_roles[$k]['name'] = $role->getName();
            $_roles[$k]['id'] = $role->getId();
        }
        $this->parser->roles = $_roles;
        $vars = array('action' => 'edit', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        if (!empty($mobuserData)) { // if we have some element
            $vars['id'] = $mobuserData->getId();
            $vars['login'] = $mobuserData->getEmail();
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/admin_mob_users_form.html", $vars);
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
            $adminIsDeleted = $this->objUser->deleteMobUser($id);
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
        clsCommon::redirect301('Location: ' . $this->path);
    }

}