<?php

namespace pages;

use classes\clsCategory;
use engine\modules\mobile\clsMobController;

class mobCategory extends clsMobController
{

    /**
     * Constructor of mobCategory class
     */
    public function __construct()
    {
        parent::__construct();

        $this->service = clsCategory::getInstance();
    }

    /**
     * Get list items by params
     *
     * @return array
     */
    public function actionIndex()
    {
        $return = array();

        $parentId = $this->_isRequestInt('parent_id', false);

        if (!$this->error->isErrors()) {
            $return = $this->service->getAll((int)$parentId);
        }

        $this->output['result'] = $return;
        return $return;
    }

    /**
     * Add new item in DB by request with data
     *
     * @return int
     */
    public function actionAdd()
    {
        $return = 0;

        // preapare data
        $parentId = $this->_isRequestInt('parent_id');
        $name = $this->_isRequestString('name');
        $description = $this->_isRequestString('description', false);
        $status = $this->_isRequestInt('status', false);

        // insert data in DB
        if (!$this->error->isErrors()) {
            $return = $this->service->addCategory($parentId, $name, $description, $status);
        }

        $this->output['result'] = array('id' => (int)$return);
        return $return;
    }

    /**
     * View data by id in request
     *
     * @return array
     */
    public function actionView()
    {
        $return = array();
        $itemId = $this->_isRequestId();

        if (!empty($itemId)) {
            $return = $this->service->getCategoryById($itemId);
        }

        $this->output['result'] = $return;
        return $return;
    }

    /**
     * Edit data item by request
     *
     * @return int
     */
    public function actionEdit()
    {
        $return = 0;

        $itemId = $this->_isRequestId();
        if (!empty($itemId)) {
            // preapare data
            $data = array('id' => $itemId);

            if ($parentId = $this->_isRequestInt('parent_id', false)) {
                $data['parent_id'] = $parentId;
            }
            if ($name = $this->_isRequestString('name', false)) {
                $data['name'] = $name;
            }
            if ($description = $this->_isRequestString('description', false)) {
                $data['description'] = $description;
            }
            if ($status = $this->_isRequestInt('status', false)) {
                $data['status'] = $status;
            }

            // update data in DB
            if (!$this->error->isErrors()) {
                $return = $this->service->updateCategory($data);

                if ($return) {
                    $this->httpStatusCode = HTTP_STATUS_OK;
                } else {
                    $this->httpStatusCode = HTTP_STATUS_NO_CONTENT;
                }
            }
        }

        $this->output['result'] = array('status' => (int)$return);
        return $return;
    }

    /**
     * Delete item by request
     *
     * @return int
     */
    public function actionDelete()
    {
        $return = 0;
        $itemId = $this->_isRequestId();

        if (!empty($itemId)) {
            $return = $this->service->deleteCategory($itemId);

            if ($return) {
                $this->httpStatusCode = HTTP_STATUS_OK;
            } else {
                $this->httpStatusCode = HTTP_STATUS_NO_CONTENT;
            }
        }
        $this->output['result'] = array('status' => (int)$return);
        return $return;
    }

}