<?php

namespace pages;

use classes\clsAdminSliders;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use classes\clsAdminCountries;

class adminSliders extends clsAdminController
{

    /**
     * @var adminSliders $instance
     */
    private static $instance = null;

    /**
     * @var clsAdminSliders $objSlider
     */
    private $objSlider = "";

    /**
     * @var string $entity
     */
    protected $entity;

    /**
     * constructor of the class adminSliders
     */
    public function __construct()
    {
        parent::__construct();
        $this->objSlider = clsAdminSliders::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('slider', ADMIN_ENTITIES_BLOCK);
        $this->parser->is_sliders_tab = true; //set active slider tab in sub menu
        $this->parser->is_pages_menu = true; //set active content in left menu
        $this->parser->current_page = ADMIN_PATH . '/sliders/'; // url path to current page
    }

    /**
     * getInstance function create or return already exists object of this class
     *
     * @return adminSliders $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminSliders();
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
        $sorter = !empty($this->get['sorter']) ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $this->parser->sliders = $this->objSlider->getSlidersList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objSlider->getSlidersListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        $this->parser->countries = clsAdminCountries::getInstance()->getCountriesList();
        return $this->parser->render('@main/pages/contents/sliders/admin/index.html');
    }

    public function actionAdd()
    {

        if (!empty($this->post['act']) && $this->post['act'] == "add") {
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
                $sliderIsUpdated = $this->objSlider->addSlider(
                    $this->post['name'],
                    $this->post['text'],
                    $this->post['subtitle'],
                    $this->post['phrase'],
                    $this->post['url']
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($sliderIsUpdated) {
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
        return $this->parser->render('@main/pages/contents/sliders/admin/add.html', $vars);
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
                $sliderIsUpdated = $this->objSlider->updateSlider(
                    $this->post['id'],
                    $this->post['name'],
                    $this->post['text'],
                    $this->post['subtitle'],
                    $this->post['phrase'],
                    $this->post['url']
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($sliderIsUpdated) {
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
        $slider = $this->objSlider->getSliderById(clsCommon::isInt($this->get['id']));
        $vars = array('action' => 'edit', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        if (!empty($slider)) { // if we have some element
            $this->parser->slider = $slider;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/contents/sliders/admin/edit.html", $vars);
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
            $sliderIsDeleted = $this->objSlider->deleteSlider($id);
            if ($sliderIsDeleted) {
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