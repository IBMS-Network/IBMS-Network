<?php

namespace pages;

use classes\clsAdminOrders;
use classes\clsAdminUsers;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use entities\Order;
use entities\OrderStatusTypes;
use entities\DeliveryHoursTypes;
use entities\OrderPaymentTypes;
use classes\clsAdmin;
use classes\clsAdminAuthorisation;
use entities\OrdersComments;
use classes\clsAdminOrdersComments;

class adminOrders extends clsAdminController
{

    /**
     * @var adminOrders $instance
     */
    private static $instance = null;

    /**
     * @var clsAdminOrders $objOrder
     */
    private $objOrder = "";

    /**
     * @var string $entity
     */
    protected $entity;

    /**
     * constractor of the class adminOrders
     */
    public function __construct()
    {
        parent::__construct();
        $this->objOrder = clsAdminOrders::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('order', ADMIN_ENTITIES_BLOCK);
        $this->parser->is_orders_tab = true; //set active order tab in sub menu
        $this->parser->is_orders_menu = true; //set active orders in left menu
        $this->parser->current_page = ADMIN_PATH . '/orders/'; // url path to current page
    }

    /**
     * getInstance function create or return already exists object of this class
     *
     * @return adminOrders $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminOrders();
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
        $sort = !empty($this->get['sort']) ? $this->get['sort'] : 'id';
        $sorter = !empty($this->get['sorter']) ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $this->parser->orders = $this->objOrder->getOrdersList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objOrder->getOrdersListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        $this->parser->order_statuses = OrderStatusTypes::getValuesByAssoc();
        $this->parser->delivery_cost = DELIVERY_COST;
        $this->parser->users = clsAdminUsers::getInstance()->getUsersList(1, ADMIN_SELECT_LIMIT);
        $this->parser->users_page = ADMIN_PATH . '/users/'; // url path to current page
        return $this->parser->render('@main/pages/orders/orders/admin/index.html');
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
                $orderIsUpdated = $this->objOrder->updateOrder(
                    $this->post['id'],
                    $this->post['name'],
                    '',
                    $this->post['description']
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($orderIsUpdated) {
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
        /** @var \entities\Order $order */
        $order = $this->objOrder->getOrderById(clsCommon::isInt($this->get['id']));
        $vars = array('action' => 'edit', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        if (!empty($order)) { // if we have some element
            $this->parser->order = $order;
            $delivery_hours = DeliveryHoursTypes::getValues();
            $this->parser->order_delivery_hours = $delivery_hours[$order->getDeliveryHours()];
            $this->parser->order_delivery_cost = DELIVERY_COST;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }
        $this->parser->order_statuses = OrderStatusTypes::getValuesByAssoc();
        $this->parser->delivery_hours = DeliveryHoursTypes::getValuesByAssoc();
        $this->parser->delivery_cost = $order->getPickup() ? 0 : DELIVERY_COST;
        return $this->parser->render("@main/pages/orders/orders/admin/edit.html", $vars);
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
            /** @var \entities\Order $order */
            $orderIsDeleted = $this->objOrder->deleteOrder($id);
            if ($orderIsDeleted) {
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

    /**
     * Ajax : change Order status
     */
    public function actionChangeStatus()
    {
        $result = array('success' => false, 'errors' => array(), 'data' => array());
        $order = $this->post['order'];
        $status = $this->post['status'];
        if (!empty($order) && !empty($status)) {
            $res = $this->objOrder->setOrderStatus($order, $status);
            if ($res !== false) {
                $result['success'] = true;
                if ($res instanceof \DateTime) {
                    $result['data'] = date_format($res, 'd.m.Y H:i');
                }
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_edit_entity_by_id',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $order)
                );
                $result['errors'][] = $actionStatus;
            }

        } else {
            $actionStatus = clsAdminCommon::getAdminMessage(
                'error_id_empty',
                ADMIN_ERROR_BLOCK,
                array('{%param}' => 'ID or status')
            );
            $result['errors'][] = $actionStatus;
        }
        echo json_encode($result);
    }

    /**
     * Ajax : get Order info
     */
    public function actionGetOrder()
    {
        $result = array('success' => false, 'errors' => array(), 'data' => array());
        $order = clsCommon::isInt($this->post['order']);
        if (!empty($order)) {
            $res = $this->objOrder->getOrderById($order);
            if ($res !== false) {
                $result['success'] = true;
                if ($res instanceof Order) {
                    $result['data'] = $res->getArrayCopy();
                    $result['data']['order_payments'] = OrderPaymentTypes::getValuesByAssoc();
                    $result['data']['order_ststuses'] = OrderStatusTypes::getValuesByAssoc();
                }
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_edit_entity_by_id',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $order)
                );
                $result['errors'][] = $actionStatus;
            }

        } else {
            $actionStatus = clsAdminCommon::getAdminMessage(
                'error_id_empty',
                ADMIN_ERROR_BLOCK,
                array('{%param}' => 'ID')
            );
            $result['errors'][] = $actionStatus;
        }
        echo json_encode($result);
    }

    /**
     * Ajax : add Comment
     */
    public function actionAddComment()
    {
        $result = array('success' => false, 'errors' => array(), 'data' => array());
        $order = clsCommon::isInt($this->post['order']);
        if (!empty($order)) {
            $adminRes = false;
            $adminSessionData = clsAdminAuthorisation::getInstance()->getAdminSession();
            if ($adminSessionData) {
                $adminRes = clsAdmin::getInstance()->getAdminById($adminSessionData['id']);
            }
            $orderRes = $this->objOrder->getOrderById($order);
            if ($orderRes !== false && $adminRes !== false) {
                $comment = $this->post['comment'] ? $this->post['comment'] : '';

                $result['success'] = clsAdminOrdersComments::getInstance()->addComment($orderRes, $adminRes, $comment);;
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_edit_entity_by_id',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $order)
                );
                $result['errors'][] = $actionStatus;
            }

        } else {
            $actionStatus = clsAdminCommon::getAdminMessage(
                'error_id_empty',
                ADMIN_ERROR_BLOCK,
                array('{%param}' => 'ID')
            );
            $result['errors'][] = $actionStatus;
        }
        echo json_encode($result);
    }
    
}