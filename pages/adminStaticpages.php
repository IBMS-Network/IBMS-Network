<?php

namespace pages;

use classes\clsAdminAuthorisation;
use classes\clsAdminStaticpages;
use classes\core\clsCommon;
use engine\clsSysValidation;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use classes\clsAdmin;

class adminStaticpages extends clsAdminController
{
    /**
     * @var \engine\modules\staticpages\clsSysStaticPage
     */
    private $staticPageModel;

    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('staticpage', ADMIN_ENTITIES_BLOCK);
        $this->staticPageModel = clsAdminStaticpages::getInstance();
        $this->parser->is_page_tab = true;
        $this->parser->is_pages_menu = true; //set active Content in left menu
        $this->parser->current_page = ADMIN_PATH . '/staticpages/'; // url path to current page
    }

    public function actionIndex()
    {
        $page = (int)$this->get['page'] > 0 && (int)$this->get['page'] < INT_MAX ? (int)$this->get['page'] : 1;
        $sort = !empty($this->get['sort']) ? $this->get['sort'] : '';
        $sorter = !empty($this->get['sorter']) ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $this->parser->pages = $this->staticPageModel->getStaticpagesList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->staticPageModel->getStaticpagesListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        $this->parser->authors = clsAdmin::getInstance()->getAdminsList();
        return $this->parser->render('@main/pages/staticpages/admin/index.html');
    }

    public function actionAdd()
    {
        if ($this->post()) {
            if (!clsSysValidation::requiredValidation($this->post('title'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Название')
                );
                $this->error->setError($error, 1, false, true);
            }
            if (!clsSysValidation::requiredValidation($this->post('slug'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Алиас')
                );
                $this->error->setError($error, 1, false, true);
            }
            if (!clsSysValidation::requiredValidation($this->post('content'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Содержание')
                );
                $this->error->setError($error, 1, false, true);
            }
            $author_id = 1;
            $adminSessionData = clsAdminAuthorisation::getInstance()->getAdminSession();
            if ($adminSessionData) {
                $author_id = $adminSessionData['id'];
            }
            $sptIsUpdated = $this->staticPageModel->addStaticPage(
                $this->post['title'],
                $this->post['content'],
                $this->post['slug'],
                $author_id
            );

            $title = addslashes(strip_tags($this->post['title']));
            if ($sptIsUpdated) {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'succ_add_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $title)
                );
                $this->error->setError($actionStatus, 1, true, true);
                clsCommon::redirect301('Location: ' . $this->parser->current_page);
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_add_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $title)
                );
                $this->error->setError($actionStatus, 1, false, true);
            }
        }
//        $this->scriptsManager->registerFile('tinyMCE', '/djs/libs/tinymce/tinymce.min.js');
//        $this->parser->scripts = $this->scriptsManager->getHTML();
        return $this->parser->render(
            '@main/pages/staticpages/admin/add.html',
            array('action_text' => clsCommon::getMessage("adding", "AdminTexts"))
        );
    }

    public function actionEdit()
    {
        # form submitted or just view
        $pageId = (int)$this->post('id') | $this->get('id');

        if ($this->post()) {
            if (!clsSysValidation::requiredValidation($this->post('title'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Title')
                );
                $this->error->setError($error, 1, false, true);
            }
            if (!clsSysValidation::requiredValidation($this->post('slug'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Slug')
                );
                $this->error->setError($error, 1, false, true);
            }
            if (!clsSysValidation::requiredValidation($this->post('content'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Content')
                );
                $this->error->setError($error, 1, false, true);
            }
            $author_id = 1;
            $adminSessionData = clsAdminAuthorisation::getInstance()->getAdminSession();
            if ($adminSessionData) {
                $author_id = $adminSessionData['id'];
            }
            $sptIsUpdated = $this->staticPageModel->updateStaticPage(
                $this->post['id'],
                $this->post['title'],
                $this->post['content'],
                $this->post['slug'],
                $author_id
            );

            $title = addslashes(strip_tags($this->post['title']));
            if ($sptIsUpdated) {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'succ_edit_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $title)
                );
                $this->error->setError($actionStatus, 1, true, true);
                clsCommon::redirect301('Location: ' . $this->parser->current_page);
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_edit_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $title)
                );
                $this->error->setError($actionStatus, 1, false, true);
            }
        }
        $page = $this->staticPageModel->getStaticPageById($pageId);
        if (!empty($page)) { // if we have some element
            $this->parser->page = $page;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render(
            '@main/pages/staticpages/admin/edit.html',
            array('action_text' => clsCommon::getMessage("editing", "AdminTexts"))
        );
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
            $sptIsDeleted = $this->staticPageModel->deleteStaticPage($id);
            if ($sptIsDeleted) {
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

