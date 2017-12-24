<?php

namespace pages;

use classes\clsAdminCountries;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;

class adminCountries extends clsAdminController
{

    /**
     * @var adminCountries $instance
     */
    private static $instance = null;

    /**
     * @var clsAdminCountries $objCountry
     */
    private $objCountry = "";

    /**
     * @var string $entity
     */
    protected $entity;

    /**
     * @var string $upload_path;
     */
    protected $upload_path = 'images/catalog/countries/';

    /**
     * constractor of the class adminCountries
     */
    public function __construct()
    {
        parent::__construct();
        $this->objCountry = clsAdminCountries::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('country', ADMIN_ENTITIES_BLOCK);
        $this->parser->is_countries_tab = true; //set active country tab in sub menu
        $this->parser->is_cats_menu = true; //set active category in left menu
        $this->parser->current_page = ADMIN_PATH . '/countries/'; // url path to current page
    }

    /**
     * getInstance function create or return already exists object of this class
     *
     * @return adminCountries $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminCountries();
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
        $this->parser->countries = $this->objCountry->getCountriesList($page, DEF_PAGING_NUM);
        $count = $this->objCountry->getCountriesListCount();
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM
        );
        return $this->parser->render('@main/pages/catalog/countries/admin/index.html');
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
                $res_img = false;
                if(!empty($_FILES)){
                    $img = $this->upload_path;
                    $filename = $this->post['name'];
                    $res_img = clsCommon::uploadImage($filename, $_FILES['img'], CONFIG_DOMAIN_PATH. $img);
                    if($res_img === true){
                        $img .= $filename;
                    }elseif(is_array($res_img)){
                        $this->error->setError($res_img[0], 1, false, true);
                        $img = '';
                    }else{
                        $img = '';
                    }
                }
                $countryIsUpdated = $this->objCountry->addCountry($this->post['name'], $img);
                $name = addslashes(strip_tags($this->post['name']));
                if ($countryIsUpdated) {
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
        return $this->parser->render('@main/pages/catalog/countries/admin/add.html', $vars);
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
                $res_img = false;
                if(!empty($_FILES)){
                    $img = $this->upload_path;
                    $filename = $this->post['name'];
                    $country = $this->objCountry->getCountryById(clsCommon::isInt($this->get['id']));
                    $todel = $country->getImg();
                    if(!empty($todel)){
                        if (file_exists(CONFIG_DOMAIN_PATH . $todel)) {
                            unlink(CONFIG_DOMAIN_PATH . $todel);
                        }
                    }
                    $res_img = clsCommon::uploadImage($filename, $_FILES['img'], CONFIG_DOMAIN_PATH. $img, true);
                    if($res_img === true){
                        $img .= $filename;
                    }elseif(is_array($res_img)){
                        $this->error->setError($res_img[0], 1, false, true);
                        $img = '';
                    }else{
                        $img = '';
                    }
                }
                $countryIsUpdated = $this->objCountry->updateCountry($this->post['id'], $this->post['name'], $img);
                $name = addslashes(strip_tags($this->post['name']));
                if ($countryIsUpdated) {
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

        $country = $this->objCountry->getCountryById(clsCommon::isInt($this->get['id']));
        $vars = array('action' => 'edit', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        if (!empty($country)) { // if we have some element
            $this->parser->country = $country;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/catalog/countries/admin/edit.html", $vars);
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
            $country = $this->objCountry->getCountryById($id);
            if(file_exists(CONFIG_DOMAIN_PATH. $country->getImg())){
                unlink(CONFIG_DOMAIN_PATH. $country->getImg());
            }
            $countryIsDeleted = $this->objCountry->deleteCountry($id);
            if ($countryIsDeleted) {
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