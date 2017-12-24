<?php

namespace pages;

use classes\clsProducts;
use engine\modules\mobile\clsMobController;

class mobProducts extends clsMobController
{

    /**
     * Constructor of mobCategory class
     */
    public function __construct()
    {
        parent::__construct();

        $this->service = clsProducts::getInstance();
    }

    /**
     * Get list items by params
     *
     * @return array
     */
    public function actionIndex()
    {
        $return = array();

        $categoryId = (int)$this->_isRequestInt('category_id');

        if (!$this->error->isErrors()) {
            $return = $this->service->getAll($categoryId);
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
        $categoryId = $this->_isRequestInt('category_id');
        $name = $this->_isRequestString('name');
        $description = $this->_isRequestString('description', false);
        $price = $this->_isRequestInt('price');
        $status = $this->_isRequestInt('status', false);

        // insert data in DB
        if (!$this->error->isErrors()) {
            $return = $this->service->addProduct($categoryId, $name, $description, $price, $status);

            if ($return && !is_null($this->imgGarbageId)) {
                $serviceId = $this->service->getServiceId();
                $oServicesImages = new clsServicesImages();
                $oServicesImages->addImage($serviceId, $return, $this->imgGarbageId);
            }
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
            $return = $this->service->getProductById($itemId);
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

            if ($categoryId = $this->_isRequestInt('category_id', false)) {
                $data['category_id'] = $categoryId;
            }
            if ($name = $this->_isRequestString('name', false)) {
                $data['name'] = $name;
            }
            if ($description = $this->_isRequestString('description', false)) {
                $data['description'] = $description;
            }
            if ($price = $this->_isRequestInt('price', false)) {
                $data['price'] = $price;
            }
            if ($status = $this->_isRequestInt('status', false)) {
                $data['status'] = $status;
            }

            // update data in DB
            if (!$this->error->isErrors()) {
                $return = $this->service->updateProduct($data);

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
            $return = $this->service->deleteProduct($itemId);

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