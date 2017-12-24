<?php

namespace pages;

use classes\clsAdminCategories;
use classes\clsValidation;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use entities\Category;
use entities\CategoryStatusTypes;

class adminCategories extends clsAdminController
{
    /**
     * @var clsAdminCategories
     */
    private $objCat;

    /** @var null|Category $ */
    private $cat = null;

    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('Categories', ADMIN_ENTITIES_BLOCK);
        $this->objCat = clsAdminCategories::getInstance();
        $this->parser->is_cats_menu = true;
        $this->parser->is_cats_tab = true; //set active categories tab in sub menu
        $this->parser->current_page = ADMIN_PATH . '/categories/';
    }

    public function actionIndex()
    {
        $page = (int)$this->get['page'] > 0 && (int)$this->get['page'] < INT_MAX ? (int)$this->get['page'] : 1;
        $sort = !empty($this->get['sort']) ? $this->get['sort'] : '';
        $sorter = !empty($this->get['sorter']) ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $filter['parent'] = empty($filter['parent']) ? null : $filter['parent'];
        $this->parser->categories = $this->objCat->getCategoriesList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objCat->getCategoriesListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        $this->parser->top_categories = $this->objCat->getCategoriesList(
            1,
            DEF_PAGING_NUM,
            '',
            'desc',
            array('parent' => null)
        );
        $this->parser->cat_statuses = CategoryStatusTypes::getValuesByAssoc();
        $tmpl_name = '@main/pages/catalog/categories/admin/index' . (!empty($filter['parent']) ? '_sub' : '') . '.html';
        return $this->parser->render($tmpl_name);
    }

    private function getStatus($status){
        return $status == 'false' || empty($status) ? false : true;
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
                $cIsUpdated = $this->objCat->addCategory(
                    $this->post('name'),
                    $this->post('parent_id'),
                    $this->post('description'),
                    $this->getStatus($this->post('status'))
                );

                $name = addslashes(strip_tags($this->post['name']));
                if ($cIsUpdated && $cIsUpdated instanceof Category) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    $this->cat = $cIsUpdated;
                    return true;
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
        return false;
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
                $cIsUpdated = $this->objCat->updateCategory(
                    (int)$this->post('id'),
                    $this->post('name'),
                    $this->post('parent_id'),
                    $this->post('description'),
                    $this->getStatus($this->post('status'))
                );

                $name = addslashes(strip_tags($this->post['name']));
                if ($cIsUpdated && $cIsUpdated instanceof Category) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    $this->cat = $cIsUpdated;
                    return true;
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
        return false;
    }

    /**
     * Delete category and subcategories
     * @return string|void
     */
    public function actionDelete()
    {
        $id = $this->get('id');
        if (empty($id)) {
            $error = clsAdminCommon::getAdminMessage(
                'error_field_empty',
                ADMIN_ERROR_BLOCK,
                array('{%fieldname}' => 'ID')
            );
            $this->error->setError($error, 1, false, true);
        }

        if (empty($error)) {
            /** @var \entities\Category $category */
            $category = $this->objCat->getCategoryById($id);

            $catIsDeleted = false;
            if (!empty($category) && $category instanceof Category) {
                // delete children
                $children = $category->getChildren();
                if (!empty($children) && count($children) > 0) {
                    foreach ($children as $child) {
                        if (!empty($child) && $child instanceof Category) {
                            $this->objCat->deleteCategory($child->getId());
                        }
                    }
                }

                // delete category
                $catIsDeleted = $this->objCat->deleteCategory($id);
                if ($catIsDeleted) {
                    // set successfully delete message
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_del_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityid}' => $this->get('id'))
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                }
            } else {
                // set not found error message
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_entity_not_found',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $id)
                );
                $this->error->setError($actionStatus, 1, false, true);
            }

            // set can not delete error message
            if (!$catIsDeleted) {
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

    /**
     * Ajax : manage categories
     */
    public function actionSend()
    {
        $result = array('result' => false, 'errors' => array(), 'object' => '');
        $status = false;
        if ($this->post['action'] == 'add') {
            $status = $this->actionAdd();
        } elseif ($this->post['action'] == 'edit') {
            $status = $this->actionEdit();
        } else {
            $actionStatus = clsAdminCommon::getAdminMessage(
                'error_action_empty',
                ADMIN_ERROR_BLOCK
            );
            $this->error->setError($actionStatus, 1, false, true);
        }
        if ($this->error->isErrors()) {
            $result['errors'] = $this->error->getError();
        }
        if ($this->cat) {
            $result['category'] = $this->cat->getArrayCopy();
        }
        $result['result'] = $status;
        echo json_encode($result);
    }
}