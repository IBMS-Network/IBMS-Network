<?php

class clsGoods {

    static private $instance = NULL;
    private $db = "";
    private $session = "";

    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsGoods();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->db = DB::getInstance();
        $this->session = clsSession::getInstance();
    }

    /**
     * @deprecated
     */
    public function getGoodsFromCategory($catId, $limit = 10) {

        $sql = "SELECT p.*, d.name as delivery_name,
            prpr.price, gp.id as group_product_id FROM products p
            LEFT JOIN deliveries d ON p.delivery_id = d.id
            JOIN productsgroups pg ON pg.product_id = p.id
            JOIN group_products gp ON gp.id = pg.group_product_id
            JOIN productprices prpr ON prpr.product_id = p.id
            JOIN users u ON u.column_number = prpr.column_number
            WHERE gp.category_id = ? AND p.status = 1 AND u.id = 4
	    LIMIT ?";
        $sqlArr = array((int) $catId, (int) $limit);
        $result = $this->db->getAll($sql, $sqlArr);

        return $result;
    }

    /**
     * Get list goods by groupId
     * 
     * @param array $groupIds
     * 
     * @return array
     */
    public function getGoodsByGroupId($groupId) {

        $select = "SELECT p.*, d.name as delivery_name, prpr.price, gp.id as group_product_id";
        $from = " FROM products p";
        $join = " LEFT JOIN deliveries d ON p.delivery_id = d.id
                JOIN productsgroups pg ON pg.product_id = p.id
                JOIN group_products gp ON gp.id = pg.group_product_id
                JOIN productprices prpr ON prpr.product_id = p.id";
        $where = " WHERE gp.id = (?) AND p.status = 1";
        $sqlArr = array((int) $groupId);
        
        // if user auth get own price
        if ($this->session->isAuthorisedUserSession()) {
            $where .= " AND u.id = ?";
            $join .= " JOIN users u ON u.column_number = prpr.column_number";
            $sqlArr[] .= $this->session->getUserIdSession();
        } else {
            $where .= " AND prpr.column_number = 1";
        }

        $sql = $select . $from . $join . $where;
        
        $result = $this->db->getAll($sql, $sqlArr);

        return $result;
    }

    /**
     * Get all list goods by ids
     * 
     * @param array $ids
     */
    public function getGoodsByIds($ids, $discount = false) {

        if (!empty($ids) && is_array($ids)) {
            $select = "SELECT p.*, d.name AS delivery_name, prpr.price, d.class as delivery_class,
                        gp.name AS groupName, gp.id as group_product_id,
                        (SELECT path FROM images i
                            JOIN entityimages ei ON ei.image_id = i.id
                            JOIN entity_types et ON ei.entity_type_id = et.id
                            WHERE et.name = 'group' AND ei.item_id = gp.id LIMIT 1) image";
            $from = " FROM products p";
            $join = " LEFT JOIN deliveries d ON p.delivery_id = d.id
                    JOIN productsgroups pg ON pg.product_id = p.id
                    JOIN group_products gp ON gp.id = pg.group_product_id
                    JOIN productprices prpr ON prpr.product_id = p.id";
            $where = " WHERE p.id IN (" . implode(',', $ids) . ") AND p.status = 1";
            
            // if user auth get own price
            if ($this->session->isAuthorisedUserSession() && !empty($discount)) {
                $where .= " AND u.id = ?";
                $join .= " JOIN users u ON u.column_number = prpr.column_number";
                $sqlArr = array((int) $this->session->getUserIdSession());
            } else {
                $where .= " AND prpr.column_number = 1";
            }

            $sql = $select . $from . $join . $where;
            if ($this->session->isAuthorisedUserSession()) {
                $res = $this->db->getAll($sql, $sqlArr);
            } else {
                $res = $this->db->getAll($sql);
            }

            if ($res) {
                return $res;
            }
        }

        return false;
    }

    /**
     * Get all list goods by groupsIds
     * 
     * @param array $groupIds
     * @param array $sortArray
     * @param bool $discount
     * 
     * @return array
     */
    public function getGoodsByGroupsIds($groupIds, $sortArray = array(), $discount = false) {

        $result = array();
        array_walk($groupIds, create_function('&$val', '$val = (int)$val;'));

        if (!empty($groupIds) && is_array($groupIds)) {
            $groupIds = join(',', $groupIds);
            $select = "SELECT p.*, d.name as delivery_name, prpr.price,
                        gp.id as group_product_id";
            $from = " FROM products p";
            $join = " LEFT JOIN deliveries d ON p.delivery_id = d.id
                    JOIN productsgroups pg ON pg.product_id = p.id
                    JOIN group_products gp ON gp.id = pg.group_product_id
                    JOIN productprices prpr ON prpr.product_id = p.id";
            $where = " WHERE gp.id IN (" . $groupIds . ") AND p.status = 1";
            
//            $order = " ORDER BY FIND_IN_SET(gp.id, '" . $groupIds . "')";
            $order = " ORDER BY prpr.price ASC";
            if (!empty($sortArray)){
                if (array_key_exists('price', $sortArray)){
                    $order = " ORDER BY prpr.price " . (($sortArray['price'] == 'asc') ? 'ASC' : 'DESC');
                }
            }
            
            // if user auth get own price
            if ($this->session->isAuthorisedUserSession() && !empty($discount)) {
                $where .= " AND u.id = ?";
                $join .= " JOIN users u ON u.column_number = prpr.column_number";
                $sqlArr = array($this->session->getUserIdSession());
            } else {
                $where .= " AND prpr.column_number = 1";
            }

            $sql = $select . $from . $join . $where . $order;
            if ($this->session->isAuthorisedUserSession()) {
                $result = $this->db->getAll($sql, $sqlArr);
            } else {
                $result = $this->db->getAll($sql);
            }
        }

        return $result;
    }

    /**
     * Get max delivery of goods by id
     * 
     * @param array $products
     */
    public function getMaxDelivery($products = array()) {

        if (is_array($products) && !empty($products)) {
            $sql = "SELECT d.name FROM products p
                JOIN deliveries d ON p.delivery_id = d.id
                WHERE p.id IN (" . implode(',', $products) . ") AND p.status = 1
                GROUP BY p.delivery_id ORDER BY d.id DESC
                LIMIT 1";
            $sqlArr = array();
            $result = $this->db->GetRow($sql);

            return $result;
        }
    }

    /**
     * Get list of popular goods by userId
     * 
     * @param int $id
     */
    public function getPopularGoods($id) {

        if (!empty($id) && is_int($id)) {

            $join2 = '';
            $select = "SELECT COUNT(op.product_id) AS products_count, p.*,
                d.name as delivery_name, q.number, prpr.price,
                gp.name as group_name, gp.id as group_product_id,
                d.class as delivery_class";
            $from = " FROM orderproducts op";
            $join = " LEFT JOIN products p ON p.id = op.product_id
                    LEFT JOIN deliveries d ON p.delivery_id = d.id
                    JOIN productsquantities pq ON p.id = pq.product_id
                    JOIN quantities q ON q.id = pq.quantity_id
                    JOIN productsgroups pg ON pg.product_id = p.id
                    JOIN group_products gp ON gp.id = pg.group_product_id
                    JOIN productprices prpr ON prpr.product_id = p.id
                    JOIN orders o ON o.id = op.order_id";
            $where = " WHERE p.status = 1";
            $other = " GROUP BY op.product_id
                ORDER BY products_count DESC
                LIMIT 5";
            $sqlArr = array();
            
            // if user auth get own price
            if ($this->session->isAuthorisedUserSession()) {
                $where2 = " AND u.id = ?";
                $join2 = " JOIN users u ON u.column_number = prpr.column_number";
                $sqlArr[] = (int) $this->session->getUserIdSession();
            } else {
                $where2 = " AND prpr.column_number = 1";
            }

            $sql = $select . $from . $join . $join2 . $where . $where2 . $other;
            $result = $this->db->getAll($sql, $sqlArr);

            if ($result) {
                return $result;
            } elseif ($this->session->isAuthorisedUserSession()) {
                $where2 = " AND prpr.column_number = 1";
                array_pop($sqlArr);

                $sql = $select . $from . $join . $where . $where2 . $other;
                $result = $this->db->getAll($sql, $sqlArr);

                if ($result) {
                    return $result;
                }
            }
        }

        return $result;
    }

    /**
     * Get list attributes of goods by id
     * 
     * @param array $ids
     */
    public function getAttributesOfGoods($ids) {

        if (!empty($ids) && is_array($ids)) {
            $sql = "SELECT a.*, ca.value, ca.item_id as product_id FROM attributes a
                    LEFT JOIN categoryattributes ca ON a.id = ca.attribute_id
                    LEFT JOIN entity_types et ON ca.entity_types_id = et.id
                    WHERE ca.item_id IN (" . implode(',', $ids) . ") AND a.status = 1
                        AND et.name = 'product'";
            $res = $this->db->getAll($sql);

            if ($res) {
                return $res;
            }
        }

        return false;
    }

    /**
     * Get list attributes of product by id
     * 
     * @param int $id
     */
    public function getAttributesOfProduct($id) {

        if (!empty($id) && is_int($id) && $id > 0) {
            $sql = "SELECT a.*, av.value, pav.product_id FROM attributes a
                    JOIN attributes_values av ON av.attribute_id = a.id
                    JOIN product_attribute_value pav ON pav.attribute_value_id = av.id
                    WHERE pav.product_id = ? AND a.status = 1";
            $sqlArr = array($id);
            $res = $this->db->getAll($sql, $sqlArr);

            if ($res) {
                return $res;
            }
        }

        return false;
    }

    /**
     * Get list products similars by ids
     * 
     * @param array $ids
     */
    public function getSimilarsIdsByGoodsIds($ids) {

        if (!empty($ids) && is_array($ids)) {
            $sql = "SELECT ps.similar_id as id FROM product_similars ps
                    JOIN products p ON ps.product_id = p.id
                    WHERE ps.product_id IN (" . implode(',', $ids) . ")
                        AND p.status = 1";
            $res = $this->db->getAll($sql);

            if ($res) {
                return $res;
            }
        }

        return false;
    }

    public function getPreviousOrderProducts($id) {

        if (is_int($id) && !empty($id)) {
            $select = "SELECT p.id, p.name, prpr.price,
                d.name as delivery_name, op.quantity as cnt, d.class as delivery_class,
                gp.name as group_name, gp.id as group_product_id,
                (SELECT path FROM images i
                    JOIN entityimages ei ON ei.image_id = i.id
                    JOIN entity_types et ON ei.entity_type_id = et.id
                    WHERE et.name = 'group' AND ei.item_id = gp.id LIMIT 1) image";
            $from = " FROM orderproducts op";
            $join = " LEFT JOIN products p ON p.id = op.product_id
                    LEFT JOIN deliveries d ON p.delivery_id = d.id
                    JOIN productsgroups pg ON pg.product_id = p.id
                    JOIN group_products gp ON gp.id = pg.group_product_id
                    JOIN productprices prpr ON prpr.product_id = p.id
                    JOIN orders o ON o.id = op.order_id";
            $where = " WHERE p.status = 1 AND op.order_id = 
                        (SELECT id from orders WHERE user_id = ?
                         ORDER BY id DESC LIMIT 1)";
            $other = " GROUP BY p.id";

            $sqlArr = array($id);
            
            // if user auth get own price
            if ($this->session->isAuthorisedUserSession()) {
                $where .= " AND u.id = ?";
                $join .= " JOIN users u ON u.column_number = prpr.column_number";
                $sqlArr[] = (int) $this->session->getUserIdSession();
            } else {
                $where .= " AND prpr.column_number = 1";
            }

            $sql = $select . $from . $join . $where . $other;
            $result = $this->db->getAll($sql, $sqlArr);

            if ($result) {
                return $result;
            }
        }

        return false;
    }

    public function getProductsByOrderId($id) {

        if (is_int($id) && !empty($id)) {
            $select = "SELECT p.id, p.name, p.description,
                d.name as delivery_name, op.quantity as cnt, d.class as delivery_class,
                gp.name as groupName, gp.id as group_product_id, prpr.price,
                (SELECT path FROM images i
                    JOIN entityimages ei ON ei.image_id = i.id
                    JOIN entity_types et ON ei.entity_type_id = et.id
                    WHERE et.name = 'group' AND ei.item_id = gp.id LIMIT 1) image";
            $from = " FROM orderproducts op";
            $join = " LEFT JOIN products p ON p.id = op.product_id
                    LEFT JOIN deliveries d ON p.delivery_id = d.id
                    JOIN productsgroups pg ON pg.product_id = p.id
                    JOIN group_products gp ON gp.id = pg.group_product_id
                    JOIN productprices prpr ON prpr.product_id = p.id
                    JOIN orders o ON o.id = op.order_id";
            $where = " WHERE p.status = 1 AND op.order_id = ?";
            $other = " /*ORDER BY p.group_product_id ASC*/";
            $sqlArr = array($id);
            
            // if user auth get own price
            if ($this->session->isAuthorisedUserSession()) {
                $where .= " AND u.id = ?";
                $join .= " JOIN users u ON u.column_number = prpr.column_number";
                $sqlArr[] = (int) $this->session->getUserIdSession();
            } else {
                $where .= " AND prpr.column_number = 1";
            }

            $sql = $select . $from . $join . $where . $other;
            $result = $this->db->getAll($sql, $sqlArr);

            if ($result) {
                return $result;
            }
        }

        return false;
    }

    public function getProductsByOrderIds($ids) {

        if (!empty($ids) && is_array($ids)) {
            $select = "SELECT p.id, p.name, d.class as delivery_class,
                d.name as delivery_name, op.quantity as cnt,
                gp.name as groupName, gp.id as group_product_id, o.id as order_id,
                prpr.price as price,
                (SELECT path FROM images i
                    JOIN entityimages ei ON ei.image_id = i.id
                    JOIN entity_types et ON ei.entity_type_id = et.id
                    WHERE et.name = 'group' AND ei.item_id = gp.id LIMIT 1) image";
            $from = " FROM orderproducts op";
            $join = " LEFT JOIN products p ON p.id = op.product_id
                    LEFT JOIN deliveries d ON p.delivery_id = d.id
                    JOIN productsgroups pg ON pg.product_id = p.id
                    JOIN group_products gp ON gp.id = pg.group_product_id
                    JOIN productprices prpr ON prpr.product_id = p.id
                    JOIN orders o ON o.id = op.order_id";
            $where = " WHERE p.status = 1 AND op.order_id IN (" . join(',', $ids) . ")";
            $other = " ORDER BY o.id DESC";
            $sqlArr = array();
            
            // if user auth get own price
            if ($this->session->isAuthorisedUserSession()) {
                $where .= " AND u.id = ?";
                $join .= " JOIN users u ON u.column_number = prpr.column_number";
                $sqlArr[] = (int) $this->session->getUserIdSession();
            } else {
                $where .= " AND prpr.column_number = 1";
            }

            $sql = $select . $from . $join . $where . $other;
            $result = $this->db->getAll($sql, $sqlArr);

            if ($result) {
                return $result;
            }
        }

        return false;
    }
    
    
    /**
     * Get goods data by ids
     * 
     * @param array $ids
     * @param array $sortArray
     * 
     * @return array
     */
    public function getAdditionalGoodsByIds($ids, $sortArray = array()) {
        $result = array();

        if (!empty($ids) && is_array($ids)) {
            $select = "SELECT pa.additional_id, p.*, pg.group_product_id,
                    prpr.price as price";
            $from = " FROM product_additionals pa ";
            $join = " JOIN products p ON pa.product_id = p.id
                    JOIN productprices prpr ON prpr.product_id = p.id
                    JOIN productsgroups pg ON pg.product_id = pa.additional_id";
            $where = " WHERE pa.product_id IN (" . join(',', $ids) . ")
                        AND p.status = 1 AND prpr.column_number = ?";
            $sqlArr = 1;
            
            $order = ' ORDER BY prpr.price ASC';
            if (!empty($sortArray)){
                if (array_key_exists('price', $sortArray)){
                    $order = " ORDER BY prpr.price " . (($sortArray['price'] == 'asc') ? 'ASC' : 'DESC');
                }
            }
            
            $sql = $select . $from . $join . $where . $order;
            $result = $this->db->getAll($sql, $sqlArr);
        }

        return $result;
    }
    
    public function getSimilarsByProductId($id) {
        $result = false;
        
        if(!empty($id) && is_int($id)) {
            $select = "SELECT p.*, d.name AS delivery_name, prpr.price, d.class as delivery_class,
                        gp.name AS groupName, gp.id as group_product_id,
                        (SELECT path FROM images i
                            JOIN entityimages ei ON ei.image_id = i.id
                            JOIN entity_types et ON ei.entity_type_id = et.id
                            WHERE et.name = 'group' AND ei.item_id = gp.id LIMIT 1) image";
            $from = " FROM products p";
            $join = " LEFT JOIN deliveries d ON p.delivery_id = d.id
                    JOIN productsgroups pg ON pg.product_id = p.id
                    JOIN group_products gp ON gp.id = pg.group_product_id
                    JOIN productprices prpr ON prpr.product_id = p.id
                    JOIN product_similars ps ON ps.similar_id = p.id";
            $where = " WHERE ps.product_id = ? AND p.status = 1";
            
            $sqlArr = array($id);
            
            // if user auth get own price
            if ($this->session->isAuthorisedUserSession() && !empty($discount)) {
                $where .= " AND u.id = ?";
                $join .= " JOIN users u ON u.column_number = prpr.column_number";
                $sqlArr[] = (int)$this->session->getUserIdSession();
            } else {
                $where .= " AND prpr.column_number = 1";
            }

            $sql = $select . $from . $join . $where;
            $result = $this->db->getAll($sql, $sqlArr);
        }
        
        return $result;
    }

}