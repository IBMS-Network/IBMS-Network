<?php

namespace pages;

use classes\clsAdminProductsHits;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use classes\clsAdminProducts;
use classes\clsAdminCategories;
use entities\Category;
use entities\Product;

class adminProducthits extends clsAdminController
{

    /**
     * @var adminProducthits $instance
     */
    private static $instance = null;

    /**
     * @var clsAdminProductsHits $objHit
     */
    private $objHit = "";

    /**
     * @var string $entity
     */
    protected $entity;

    /**
     * constructor of the class adminProducthits
     */
    public function __construct()
    {
        parent::__construct();
        $this->objHit = clsAdminProductsHits::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('product_hits', ADMIN_ENTITIES_BLOCK);
        $this->parser->is_hits_tab = true; //set active product hits tab in sub menu
        $this->parser->is_cats_menu = true; //set active category in left menu
        $this->parser->current_page = ADMIN_PATH . '/producthits/'; // url path to current page
    }

    /**
     * getInstance function create or return already exists object of this class
     *
     * @return adminProducthits $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminProducthits();
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
        $this->parser->hits = $this->objHit->getProductsHitsList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objHit->getProductsHitsListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        return $this->parser->render('@main/pages/catalog/hits/admin/index.html');
    }

    public function actionAdd()
    {

        if (!empty($this->post['act']) && $this->post['act'] == "add") {
            $hit_id = (int)$this->post['product_select_id'] > 0 ? (int)$this->post['product_select_id'] : (int)$this->post['product_id'];
            $hitIsUpdated = $this->objHit->addProductsHits($hit_id, $this->post['product_articul']);
            if ($hitIsUpdated) {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'succ_add_entity_by_id',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $hitIsUpdated)
                );
                $this->error->setError($actionStatus, 1, true, true);
                clsCommon::redirect301('Location: ' . $this->parser->current_page);
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_add_entity_by_id',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $hit_id)
                );
                $this->error->setError($actionStatus, 1, false, true);
            }

            clsCommon::redirect301('Location: ' . $this->parser->current_page . 'add');
        }

        $this->parser->categories = clsAdminCategories::getInstance()->getCategoriesList(
            1,
            ADMIN_SELECT_LIMIT,
            '',
            'desc',
            array('parent' => null)
        );
        $vars = array('action' => 'add', 'action_text' => clsCommon::getMessage("adding", "AdminTexts"));
        return $this->parser->render('@main/pages/catalog/hits/admin/add.html', $vars);
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
            $prodIsDeleted = $this->objHit->deleteProductsHits($id);
            if ($prodIsDeleted) {
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

    public function actionGetsub()
    {
        $result = array('success' => false, 'error' => 'unknown');
        $id = clsCommon::isInt($this->post['category']);
        if (!empty($id)) {
            $category = clsAdminCategories::getInstance()->getCategoryById($id);
            if ($category) {
                $subs = array();
                $_subs = clsAdminCategories::getInstance()->getCategoriesList(
                    1,
                    ADMIN_SELECT_LIMIT,
                    '',
                    '',
                    array('parent' => $id)
                );
                if (!empty($_subs)) {
                    foreach ($_subs as $c) {
                        if (!empty($c) && $c instanceof Category) {
                            $subs[$c->getId()] = $c->getName();
                        }
                    }
                    $result['subs'] = $subs;
                    $result['success'] = true;
                } else {
                    $result['error'] = clsAdminCommon::getAdminMessage(
                        'error_not_get_categories',
                        ADMIN_ERROR_BLOCK
                    );
                }

            } else {
                $result['error'] = clsAdminCommon::getAdminMessage(
                    'error_not_get_categories',
                    ADMIN_ERROR_BLOCK
                );
            }
        } else {
            $result['subs'] = array();
            $result['success'] = true;
        }
        echo json_encode($result);
    }

    public function actionGetproduct()
    {
        $result = array('success' => false, 'error' => 'unknown');
        $id = clsCommon::isInt($this->post['category']);
        if (!empty($id)) {
            $category = clsAdminCategories::getInstance()->getCategoryById($id);
            if ($category) {
                $prods = array();
                $_prods = clsAdminProducts::getInstance()->getProductsList(
                    1,
                    ADMIN_SELECT_LIMIT,
                    '',
                    '',
                    array('category' => $id)
                );
                if (!empty($_prods)) {
                    foreach ($_prods as $p) {
                        if (!empty($p) && $p instanceof Product) {
                            $prods[$p->getId()] = $p->getName();
                        }
                    }
                    $result['prods'] = $prods;
                    $result['success'] = true;
                } else {
                    $result['error'] = clsAdminCommon::getAdminMessage(
                        'error_not_get_products',
                        ADMIN_ERROR_BLOCK
                    );
                }

            } else {
                $result['error'] = clsAdminCommon::getAdminMessage(
                    'error_not_get_categories',
                    ADMIN_ERROR_BLOCK
                );
            }
        } else {
            $result['prods'] = array();
            $result['success'] = true;
        }
//        echo "<pre>";print_r($result);echo "</pre>";
        echo json_encode($result);
    }

}