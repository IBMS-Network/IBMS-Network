<?php

class clsPagesmetas {

    static private $instance = NULL;
    private $db = "";

    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsPagesmetas();
        }
        return self::$instance;
    }

    public function clsPagesmetas() {
        $this->db = DB::getInstance();
    }

    /**
     * Clear row meta for item
     * 
     * @param integer $iPageTypeId
     * @param integer $iItemId
     * @param integer $iMetaId
     */
    public function clearByParams($iPageTypeId, $iItemId) {
        $sql = "DELETE FROM `pagesmetas` WHERE page_type_id = ? AND item_id = ?";

        $sqlArr = array((int) $iPageTypeId, (int) $iItemId);
        $result = $this->db->Execute($sql, $sqlArr);

        return $result;
    }

    /**
     * Add row meta for item
     * 
     * @param integer $iPageTypeId
     * @param integer $iItemId
     * @param integer $iMetaId
     * @param string $sValue
     * @param string $sAttributes
     * 
     * @return integer
     */
    public function addMeta($iPageTypeId, $iItemId, $iMetaId, $sValue, $sAttributes = '') {
        $sql = "INSERT INTO `pagesmetas` (page_type_id, item_id, meta_id, value, attributes) VALUES (?, ?, ?, ?, ?)";

        $sqlArr = array((int) $iPageTypeId, (int) $iItemId, (int) $iMetaId, $sValue, $sAttributes);
        $res = $this->db->Execute($sql, $sqlArr);

        return $this->db->Insert_ID();
    }

    /**
     * Add rows meta for item
     * 
     * @param integer $iPageTypeId
     * @param integer $iItemId
     * @param integer $iMetas
     * 
     */
    public function addMetas($iPageTypeId, $iItemId, $iMetas) {
        $sql = "INSERT INTO `pagesmetas` (page_type_id, item_id, meta_id, value, attributes) VALUES ";

        foreach ($iMetas As $key => $value) {
//            $value = array_shift($value);
            $metaId = clsApiParser::getMetaTypeIdByName($key);

//            if (empty($metaId)) {
//                $this->api->log(3, sprintf('Method: %s, Line: %s => Meta ID don\'t exists!', __METHOD__, __LINE__));
//                continue;
//            }

            $str[] = "($iPageTypeId, $iItemId, $metaId, '$value', '')";
        }
        $sql .= implode(', ', $str);

        $res = $this->db->Execute($sql);

        return $res;
    }

}