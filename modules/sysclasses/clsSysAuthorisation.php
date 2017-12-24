<?php

namespace engine;

use classes\core\clsDB;
use classes\clsSession;
use classes\clsAdmin;

/**
 * Administrator authorization class.
 *
 * @author Anatoly.Bogdanov
 *
 */
class clsSysAuthorisation {

    /**
     * Inner variable to hold own object of a class
     *
     * @var object $instance - object of the clsAuthorisation
     */
    private static $instance = NULL;

    /**
     * variable of DB class , present DB connect
     *
     * @var $db object
     */
    private $db = "";

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em = null;

    private $objSession = "";

    //        private $objValidation = "";
    private $objClient = "";

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance(){

        if( self::$instance == NULL ){
            self::$instance = new clsSysAuthorisation();
        }

        return self::$instance;
    }

    /**
     * Constructor for clsAuthorisation class
     *
     */
    public function __construct(){
        $this->em = clsDB::getInstance();
        $this->objSession = clsSession::getInstance();
    }

    public function login( $email, $pass ){
        $pass = password_hash( $pass, PASSWORD_BCRYPT, array( "cost" => 10 ) );
        $user = $this->em->getRepository( 'entities\User' )->findOneBy( array( 'email' => $email, 'password' => $pass ) );

        if( $user ){
            $this->objSession->setUserSession( $email, $pass );

            return array( 'result' => true );
        }else{
            return array( 'result' => false );
        }
    }

    /**
     * Authorisation in adminpanel
     *
     * @param string $login
     * administrator login
     * @param string $password
     * administrator password
     */
    public function loginAdmin( $login, $password ){
        $errors = array();
        $admin = false;
        $admin = clsAdmin::getInstance()->getAdminDataByAuth( $login, $password );

        if( !empty( $admin ) && is_array( $admin ) ){
            $this->setAdminSession( $admin );
            header( 'Location: /admin/' );
        }else{
            return false;
        }
    }

    public function destroyAdminSession(){
        $this->objSession->ClearParams( 'admin_user' );
    }

    public function setAdminSession( $admin ){
        $res = false;
        if( !empty( $admin ) && is_array( $admin ) ){
            $_sess = array();
            foreach( $admin as $value ){
                if( empty( $_sess ) ){
                    $_sess['id'] = $value['id'];
                    $_sess['admin_name'] = $value['login'];
                    $_sess['role_name'] = $value['role_name'];
                }
                $_sess['permissions'][$value['perm_id']] = $value['name'];
            }
            $this->objSession->SetParams( $_sess, 'admin_user' );
            $res = true;
        }
        return $res;
    }

    public function logout(){

        $session = clsSession::getInstance();
        $session->destroyUserSession();
    }

    public function logoutAdmin(){
        $this->destroyAdminSession();
    }

}