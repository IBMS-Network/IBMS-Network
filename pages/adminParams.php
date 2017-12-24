<?php

namespace pages;

use classes\clsAdminParams;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;

/**
 * Class for admin entity adminParam. Set actions under the Admin Params.
 * @author Anatoly.Bogdanov
 *
 */
class adminParams extends clsAdminController
{

    /**
     * self object
     *
     * @var adminParams $instance
     */
    private static $instance = null;

    /**
     * Object of the clsAdminParams class
     *
     * @var clsAdminParams $objParam
     */
    private $objParam = "";

    /**
     * Constructor of the class of controller.
     * Set entity name, get object of the Param, set menu item and tab item active
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('param', ADMIN_ENTITIES_BLOCK);
        $this->objParam = clsAdminParams::getInstance();
        $this->parser->is_param_tab = true; //set active param tab in sub menu
        $this->parser->is_params_menu = true; //set active Parameters in left menu
        $this->parser->current_page = ADMIN_PATH . '/params/'; // url path to current page
    }

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminParams();
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
        $this->parser->params = $this->objParam->getParamsList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objParam->getParamsListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        return $this->parser->render('@main/pages/params/admin/admin_params.html');
    }

    /**
     * add Param controller
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
                $paramIsUpdated = $this->objParam->addParam($this->post['name'], $this->post['value']);
                $name = addslashes(strip_tags($this->post['name']));
                if ($paramIsUpdated) {
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
        return $this->parser->render('@main/pages/params/admin/admin_params_form.html', $vars);
    }

    /**
     * Edit the Param controller
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

                $paramIsUpdated = $this->objParam->updateParam(
                    $this->post['id'],
                    $this->post['name'],
                    $this->post['value']
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($paramIsUpdated) {
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

        $paramData = $this->objParam->getParamById(clsCommon::isInt($this->get['id']));
        if ($paramData) {
            $this->parser->param = $paramData;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/params/admin/admin_params_form.html", $vars);
    }

    /**
     * Delete the Param controller
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
            $paramIsDeleted = $this->objParam->deleteParam($id);
            if ($paramIsDeleted) {
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