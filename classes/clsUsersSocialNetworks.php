<?php

class clsUsersSocialNetworks {

    /**
     * Table name
     * @var $tableName string
     */
    private $tableName = 'users_social_networks';

    /**
     * Inner variable to hold own object of a class
     * @var object $instance
     */
    static private $instance = NULL;

    /**
     * variable of DB class , present DB connect
     * @var $db object
     */
    private $db = "";

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsUsersSocialNetworks();
        }
        return self::$instance;
    }

    /**
     * Constructor for clsStatic class
     *
     */
    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function clearSocialsByUserId($iUserId) {
        if(!empty($iUserId) && is_int($iUserId)) {
            $sql = "DELETE FROM " . $this->tableName . " WHERE user_id = ?";
            $this->db->Execute($sql, array($iUserId));
        }
    }

    public function addUserSocials($aData, $iUserId) {
        $usersData = array();
        if(!empty($aData) && is_array($aData) && !empty($iUserId) && is_int($iUserId)){
            foreach($aData as $k => $v) {
                if(strpos($k, 'ID') !== false){
                    $usersData[str_replace('ID', '', $k)]['id'] = $v;
                } elseif(strpos($k, 'Token') !== false) {
                    $usersData[str_replace('Token', '', $k)]['token'] = $v;
                }
            }
            
            if(!empty($usersData)) {
                foreach($usersData as $k => $v) {
                    $sql = "INSERT INTO
                            users_social_networks(user_id, network_id, user_network_id, token)
                            VALUES (?, (SELECT id FROM social_networks WHERE name = ?), ?, ?)";
                    $sqlArr = array($iUserId, $k, $v['id'], !empty($v['token']) ? $v['token'] : '');
                    $this->db->Execute($sql, $sqlArr);
                }
            }

            return $this->db->Insert_ID();
        }
    }
}