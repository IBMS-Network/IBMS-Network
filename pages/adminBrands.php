<?php

namespace pages;

use classes\clsAdminBrands;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use classes\clsAdminCountries;

class adminBrands extends clsAdminController
{

    /**
     * @var adminBrands $instance
     */
    private static $instance = null;

    /**
     * @var clsAdminBrands $objBrand
     */
    private $objBrand = "";

    /**
     * @var string $entity
     */
    protected $entity;

    /**
     * @var string $upload_path ;
     */
    protected $upload_path = 'images/catalog/brands/';

    /**
     * constructor of the class adminBrands
     */
    public function __construct()
    {
        parent::__construct();
        $this->objBrand = clsAdminBrands::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('brand', ADMIN_ENTITIES_BLOCK);
        $this->parser->is_brands_tab = true; //set active brand tab in sub menu
        $this->parser->is_cats_menu = true; //set active category in left menu
        $this->parser->current_page = ADMIN_PATH . '/brands/'; // url path to current page
    }

    /**
     * getInstance function create or return already exists object of this class
     *
     * @return adminBrands $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminBrands();
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
        $this->parser->brands = $this->objBrand->getBrandsList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objBrand->getBrandsListCount($filter);
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
        return $this->parser->render('@main/pages/catalog/brands/admin/index.html');
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
                if (!empty($_FILES)) {
                    $img = $this->upload_path;
                    $filename = $this->post['name'];
                    $res_img = clsCommon::uploadImage($filename, $_FILES['img'], CONFIG_DOMAIN_PATH . $img);
                    if ($res_img === true) {
                        $img .= $filename;
                    } elseif (is_array($res_img)) {
                        $this->error->setError($res_img[0], 1, false, true);
                        $img = '';
                    } else {
                        $img = '';
                    }
                }
                $brandIsUpdated = $this->objBrand->addBrand(
                    $this->post['name'],
                    $img,
                    $this->post['description'],
                    $this->post['country']
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($brandIsUpdated) {
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
        $this->parser->countries = clsAdminCountries::getInstance()->getCountriesList();
        $vars = array('action' => 'add', 'action_text' => clsCommon::getMessage("adding", "AdminTexts"));
        return $this->parser->render('@main/pages/catalog/brands/admin/add.html', $vars);
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
                $img = '';
                if (!empty($_FILES) && !empty($_FILES['img']['tmp_name'])) {
                    $img = $this->upload_path;
                    $filename = $this->post['name'];
                    $brand = $this->objBrand->getBrandById(clsCommon::isInt($this->get['id']));
                    $todel = $brand->getImg();
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
                $brandIsUpdated = $this->objBrand->updateBrand(
                    $this->post['id'],
                    $this->post['name'],
                    $img,
                    $this->post['description'],
                    $this->post['country']
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($brandIsUpdated) {
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
        $brand = $this->objBrand->getBrandById(clsCommon::isInt($this->get['id']));
        $vars = array('action' => 'edit', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        if (!empty($brand)) { // if we have some element
            $this->parser->brand = $brand;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }
        $this->parser->countries = clsAdminCountries::getInstance()->getCountriesList();

        return $this->parser->render("@main/pages/catalog/brands/admin/edit.html", $vars);
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
            $brand = $this->objBrand->getBrandById($id);
            if (file_exists(CONFIG_DOMAIN_PATH . $brand->getImg())) {
                unlink(CONFIG_DOMAIN_PATH . $brand->getImg());
            }
            $brandIsDeleted = $this->objBrand->deleteBrand($id);
            if ($brandIsDeleted) {
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