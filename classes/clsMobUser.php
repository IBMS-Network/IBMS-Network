<?php

namespace classes;

use classes\core\clsDB;
use classes\core\clsCommon;
use engine\clsSysCommon;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query;

/**
 * clsMobUser class perform methods and action for \entities\AclMobileUser
 */
class clsMobUser
{

    const STATUS_USER_ALREADY_EXISTS = -1;

    const STATUS_USER_DATA_INVALID = -2;

    /**
     * Inner variable to hold own object of a class
     *
     * @var object $instance - object of the clsUser
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

    private $partnerID = "";

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if( self::$instance == NULL ){
            self::$instance = new clsMobUser();
        }

        return self::$instance;
    }

    /**
     * Constructor for clsMobUser class
     *
     */
    public function __construct()
    {
        $this->em = clsDB::getInstance();
    }

    /**
     * Create user item
     *
     * @param string $email
     * @param string $pass
     * @param string $roleName
     *
     * @return integer
     */
    public function createUser( $email, $pass, $roleName = 'quest' )
    {
        $return = 0;
        if( $email && $pass ){
            $user = false;
            $pass = password_hash( $pass, PASSWORD_BCRYPT, array( "cost" => 10 ) );
            $user = $this->em->getRepository('entities\AclMobileUser')->findOneBy(array('email' => $email));

            if( $user ){
                $return = static::STATUS_USER_ALREADY_EXISTS;
            }

            $user = new \entities\AclMobileUser();
            $user->setEmail( $email );
            $user->setPassword( $pass );
            $role = false;
            $role = $this->em->getRepository('\entities\AclMobileRole')->findOneBy(array('name' => $roleName));
            if( $role ){
                $user->setRole( $role );
            }
            $this->em->persist( $user );
            $this->em->flush();

            $return = $user->getId();
        }else{
            $return = static::STATUS_USER_DATA_INVALID;
        }
        return $return;
    }

    /**
     * Get user data
     * by user access token
     *
     * @param string $token
     *
     * @return array
     */
    public function getUserInfoByToken( $token )
    {
        $user = $this->em->getRepository( '\entities\AclMobileUser' )->findOneBy( array( 'token' => $token ) );
        return $user;
    }

    /**
     * Get user data by e-mail & password
     *
     * @param string $email
     * user e-mail
     * @param string $password
     * user password
     *
     * @return array|false
     */
    public function getUserInfo( $email, $password )
    {
        $res = false;
        if( !empty( $email ) && !empty( $password ) ){
            $email = htmlspecialchars( $email );
            $qb = $this->em->createQueryBuilder();
            $qb->select( 'u' )->from( 'entities\AclMobileUser', 'u' )->where( 'u.email = :login' )->setParameter( 'login', $email )->leftJoin( 'entities\AclMobileRole', 'r', Join::WITH, $qb->expr()->eq( 'u.role', 'r.id' ) )->leftJoin( 'entities\AclMobilePermissionrole', 'rp', Join::WITH, $qb->expr()->eq( 'u.role', 'rp.roleId' ) )->leftJoin( 'entities\AclMobilePermission', 'p', Join::WITH, $qb->expr()->eq( 'rp.permId', 'p.id' ) )->setMaxResults( 1 );
            $res = $qb->getQuery()->getOneOrNullResult();
            if( $res ){
                if( !password_verify( $password, $res->getPassword() ) ){
                    $res = false;
                }
            }
        }

        return $res;
    }

    /**
     * Get user data by id
     *
     * @param integer $id
     *
     * @return array
     */
    public function getUserById( $id )
    {
        $user = $this->em->getRepository( '\entities\AclMobileUser' )->find( (int) $id )->getArrayCopy();

        return $user;
    }

    public function updateUser( $data = array(), $editorIsAdmin = 0 )
    {
        $return = false;
        if( !empty( $data ) && is_array( $data ) ){
            $user_id = (int) $data['id'];
            unset( $data['id'] );

            $user = $this->em->getRepository( 'entities\User' )->find( $user_id );
            if( $user && !empty( $data ) ){

                if( isset( $data['email'] ) ){
                    $user->setEmail( $data['email'] );
                }
                if( isset( $data['password'] ) && !empty( $data['password'] ) ){
                    $pass = password_hash( $data['password'], PASSWORD_BCRYPT, array( "cost" => 10 ) );
                    $user->setPassword( $pass );
                }

                $this->em->persist( $user );
                $this->em->flush();

                $return = true;
            }
        }
        return $return;
    }

    /**
     * Get list data
     *
     * @param integer $count
     * count items in list
     * @param integer $offset
     * offset start position in list
     *
     * @return array
     */
    public function getList( $count = 10, $offset = 0 )
    {
        $count = clsSysCommon::isInt( $count );
        $offset = clsSysCommon::isInt( $offset );

        $return = array();

        if( !empty( $count ) ){
            $return = $this->em->getRepository( 'entities\AclMobileUser' )->findBy( array(), array(), $count, $offset );

            array_walk( $return, create_function( '&$val', '$val = $val->getArrayCopy();' ) );
        }

        return $return;
    }

    /**
     * Delete user item in DB
     *
     * @param integer $user_id
     *
     * @return boolean
     */
    public function deleteUser( $user_id )
    {
        $result = false;
        if( clsCommon::isInt( $user_id ) ){
            $user = $this->em->getRepository( 'entities\AclMobileUser' )->find( (int) $user_id );
            if( $user ){
                $this->em->remove( $user );
                $this->em->flush();
                $result = true;
            }
        }
        return (bool) $result;
    }

    /**
     * Set token for exists user by id
     *
     * @param integer $user_id
     * @param string $token
     *
     * @return boolean
     */
    public function setToken( $user_id, $token = '' )
    {
        $result = false;
        if( clsCommon::isInt( $user_id ) ){
            $user = $this->em->getRepository( 'entities\AclMobileUser' )->find( (int) $user_id );

            $user->setToken( $token );
            $this->em->persist( $user );
            $this->em->flush();

            $result = (bool) $user->getId();
        }

        return $result;
    }

    /**
     * Get token value for exists user by id
     *
     * @param integer $user_id
     *
     * @return string
     */
    public function getToken( $user_id )
    {
        $result = '';
        if( clsCommon::isInt( $user_id ) ){
            $user = $this->em->getRepository( 'entities\AclMobileUser' )->find( (int) $user_id );
            if( $user ){
                $result = $user->getToken();
            }
        }

        return $result;
    }

}