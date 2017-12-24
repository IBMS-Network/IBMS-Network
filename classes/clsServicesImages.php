<?php
class clsServicesImages {
    
    /**
     * Database
     * 
     * @var DB
     */
    private $db = "";
    
    /**
     * Constructor 
     * 
     * @return clsServicesImages
     */
    public function __construct() {
        $this->db = DB::getInstance();
    }
    
    /**
     * Add join row for image
     * 
     * @param integer $serviceId
     * @param integer $itemId
     * @param integer $imageId
     * 
     * @return integer
     */
    public function addImage($serviceId = 0, $itemId = 0, $imageId = 0) {
        $return = 0;
        if (clsSysCommon::isInt($serviceId) && clsSysCommon::isInt($serviceId) && clsSysCommon::isInt($serviceId)) {
            $sql = "INSERT INTO `servicesimages` (service_id, item_id, image_id) 
    					VALUES (?, ?, ?)
    					ON DUPLICATE KEY UPDATE image_id = ?";
            $sqlArr = array((int)$serviceId, (int)$itemId, (int)$imageId, (int)$imageId);
            $result = $this->db->Execute($sql, $sqlArr);
            
            $return = (int)$this->db->Insert_ID();
        }
        return $return;
    }
    
} // class clsServicesImages
