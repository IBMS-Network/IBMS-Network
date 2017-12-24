<?php

namespace pages;

use classes\clsAdminAuthorisation;
use classes\clsAdminNews;
use classes\core\clsCommon;
use engine\clsSysValidation;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use classes\clsAdmin;

class adminNews extends clsAdminController
{
    /**
     * @var string $upload_path ;
     */
    protected $upload_path = 'images/catalog/news/';

    /**
     * @var \classes\clsAdminNews
     */
    private $objNews;

    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('news', ADMIN_ENTITIES_BLOCK);
        $this->objNews = clsAdminNews::getInstance();
        $this->parser->is_news_tab = true;
        $this->parser->is_pages_menu = true; //set active Content in left menu
        $this->parser->current_page = ADMIN_PATH . '/news/'; // url path to current page
    }

    public function actionIndex()
    {
        $page = (int)$this->get['page'] > 0 && (int)$this->get['page'] < INT_MAX ? (int)$this->get['page'] : 1;
        $sort = !empty($this->get['sort']) ? $this->get['sort'] : '';
        $sorter = !empty($this->get['sorter']) ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $this->parser->news = $this->objNews->getNewsList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objNews->getNewsListCount($filter);
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
        return $this->parser->render('@main/pages/news/admin/index.html');
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
            if (!clsSysValidation::requiredValidation($this->post('content'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Содержание')
                );
                $this->error->setError($error, 1, false, true);
            }

            // set author
            $author_id = 1;
            $adminSessionData = clsAdminAuthorisation::getInstance()->getAdminSession();
            if ($adminSessionData) {
                $author_id = $adminSessionData['id'];
            }

            // set image
            $img = '';
            if (!empty($_FILES) && !empty($_FILES['img']['tmp_name'])) {
                $img = $this->upload_path;
                $filename = $this->post['name'];
                $res_img = clsCommon::uploadImage($filename, $_FILES['img'], CONFIG_DOMAIN_PATH . $img, true);
                if ($res_img === true) {
                    $img .= $filename;
                } elseif (is_array($res_img)) {
                    $this->error->setError($res_img[0], 1, false, true);
                    $img = '';
                } else {
                    $img = '';
                }
            }

            $newsIsUpdated = $this->objNews->addNews(
                $this->post['title'],
                $this->post['content'],
                $author_id,
                $img
            );

            $title = addslashes(strip_tags($this->post['title']));
            if ($newsIsUpdated) {
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
            clsCommon::redirect301('Location: ' . $this->parser->current_page . 'add/');
        }
        return $this->parser->render(
            '@main/pages/news/admin/add.html',
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

            // set img
            $img = '';
            if (!empty($_FILES) && !empty($_FILES['img']['tmp_name'])) {
                $img = $this->upload_path;
                $filename = $this->post['title'];
                /** @var \entities\News $news_one */
                $news_one = $this->objNews->getNewsById($pageId);
                $todel = $news_one->getImg();
                if (!empty($todel)) {
                    if (file_exists(CONFIG_DOMAIN_PATH . $todel)) {
                        unlink(CONFIG_DOMAIN_PATH . $todel);
                    }
                }
                $res_img = clsCommon::uploadImage($filename, $_FILES['img'], CONFIG_DOMAIN_PATH . $img, true);
                if ($res_img === true) {
                    $img .= $filename;
                } elseif (is_array($res_img)) {
                    $this->error->setError($res_img[0], 1, false, true);
                    $img = '';
                } else {
                    $img = '';
                }
            }

            $newsIsUpdated = $this->objNews->updateNews(
                (int)$this->post['id'],
                $this->post['title'],
                $this->post['content'],
                $author_id,
                $img
            );

            $title = addslashes(strip_tags($this->post['title']));
            if ($newsIsUpdated) {
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
            clsCommon::redirect301('Location: ' . $this->parser->current_page . 'edit/id=' . (int)$this->post['id']);
        }
        $news = $this->objNews->getNewsById($pageId);
        if (!empty($news)) { // if we have some element
            $this->parser->news = $news;
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
            '@main/pages/news/admin/edit.html',
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
            $newsIsDeleted = $this->objNews->deleteNews($id);
            if ($newsIsDeleted) {
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

