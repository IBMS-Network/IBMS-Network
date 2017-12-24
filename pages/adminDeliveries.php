<?php

namespace pages;

use classes\clsAdminDeliveries;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;

/**
 * Class for admin entity adminDelivery. Set actions under the Admin Deliveries.
 * @author Anatoly.Bogdanov
 *
 */
class adminDeliveries extends clsAdminController
{

    /**
     * self object
     *
     * @var adminDeliveries $instance
     */
    private static $instance = null;

    /**
     * Object of the clsAdminDeliveries class
     *
     * @var clsAdminDeliveries $objDelivery
     */
    private $objDelivery = "";

    /**
     * Constructor of the class of controller.
     * Set entity name, get object of the Delivery, set menu item and tab item active
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('delivery', ADMIN_ENTITIES_BLOCK);
        $this->objDelivery = clsAdminDeliveries::getInstance();
        $this->parser->is_delivery_tab = true; //set active delivery tab in sub menu
        $this->parser->is_pages_menu = true; //set active Content in left menu
        $this->parser->current_page = ADMIN_PATH . '/deliveries/'; // url path to current page
    }

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminDeliveries();
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
        $sorter = !empty($this->get['sorter'])  ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $this->parser->deliveries = $this->objDelivery->getDeliveriesList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objDelivery->getDeliveriesListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        return $this->parser->render('@main/pages/deliveries/admin/admin_deliveries.html');
    }

    /**
     * add Delivery controller
     * @see engine\modules\admin.clsAdminController::actionAdd()
     */
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
                $deliveryIsUpdated = $this->objDelivery->addDelivery($this->post['name'], $this->post['value']);
                $name = addslashes(strip_tags($this->post['name']));
                if ($deliveryIsUpdated) {
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
        return $this->parser->render('@main/pages/deliveries/admin/admin_deliveries_form.html', $vars);
    }

    /**
     * Edit the Delivery controller
     * @see engine\modules\admin.clsAdminController::actionEdit()
     */
    public function actionEdit()
    {
        $error = '';
        if (!empty($this->post['act']) && $this->post['act'] == "update") {

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

                $deliveryIsUpdated = $this->objDelivery->updateDelivery(
                    $this->post['id'],
                    $this->post['name'],
                    $this->post['value']
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($deliveryIsUpdated) {
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

        $vars = array('action' => 'update', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        $deliveryData = $this->objDelivery->getDeliveryById(clsCommon::isInt($this->get['id']));
        if ($deliveryData) {
            $this->parser->delivery = $deliveryData;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/deliveries/admin/admin_deliveries_form.html", $vars);
    }

    /**
     * Delete the Delivery controller
     * @see engine\modules\admin.clsAdminController::actionDelete()
     */
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
            $deliveryIsDeleted = $this->objDelivery->deleteDelivery($id);
            if ($deliveryIsDeleted) {
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