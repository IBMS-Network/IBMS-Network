<?php

namespace pages;

use classes\clsAdminModels;
use classes\clsValidation;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;

class adminModels extends clsAdminController
{
    /**
     * @var clsAdminModels
     */
    private $objModel;

    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('model', ADMIN_ENTITIES_BLOCK);
        $this->objModel = clsAdminModels::getInstance();
        $this->parser->is_cats_menu = true;
        $this->parser->is_models_tab = true; //set active models tab in sub menu
        $this->parser->current_page = ADMIN_PATH . '/models/';
    }

    public function actionIndex()
    {
        $page = (int)$this->get['page'] > 0 && (int)$this->get['page'] < 4294967295 ? (int)$this->get['page'] : 1;
        $this->parser->models = $this->objModel->getModelsList($page, DEF_PAGING_NUM);
        $count = $this->objModel->getModelsListCount();
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM
        );
        return $this->parser->render('@main/pages/catalog/models/admin/index.html');
    }

    public function actionAdd()
    {
        if ($this->post()) {
            if (!clsValidation::requiredValidation($this->post('name'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Name')
                );
                $this->error->setError($error, 1, false, true);
            }

            if (!$this->error->isErrors()) {
                $cIsUpdated = $this->objModel->addModel(
                    $this->post('name')
                );

                $name = addslashes(strip_tags($this->post['name']));
                if ($cIsUpdated) {
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
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_add_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $this->post('name'))
                );
                $this->error->setError($actionStatus, 1, false, true);
            }
        }
        $this->parser->models = $this->objModel->getModelsList(1, 1000);
        return $this->parser->render(
            '@main/pages/catalog/models/admin/add.html',
            array('action_text' => clsCommon::getMessage("adding", "AdminTexts"))
        );
    }

    public function actionEdit()
    {
        if ($this->post()) {
            if (!clsValidation::requiredValidation($this->post('name'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Name')
                );
                $this->error->setError($error, 1, false, true);
            }

            if (!$this->error->isErrors()) {
                $cIsUpdated = $this->objModel->updateModel(
                    (int)$this->post('id'),
                    $this->post('name')
                );

                $name = addslashes(strip_tags($this->post['name']));
                if ($cIsUpdated) {
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
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_edit_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $this->post('name'))
                );
                $this->error->setError($actionStatus, 1, false, true);
            }
        }

        $this->parser->model = $this->objModel->getModelById((int)$this->get('id'));
        return $this->parser->render(
            '@main/pages/catalog/models/admin/edit.html',
            array('action_text' => clsCommon::getMessage("editing", "AdminTexts"))
        );
    }

    public function actionDelete()
    {
        $this->objModel->deleteModel($this->get('id'));
        $actionStatus = clsAdminCommon::getAdminMessage(
            'succ_del_entity',
            ADMIN_ERROR_BLOCK,
            array('{%entity}' => $this->entity, '{%entityid}' => $this->get('id'))
        );
        $this->error->setError($actionStatus, 1, true, true);
        clsCommon::redirect301('Location: ' . $this->parser->current_page);
    }
}