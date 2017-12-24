<?php

namespace classes;

use engine\clsAdminEntity;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use classes\core\clsDB;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query;
use entities\AclMobileUser;

/**
 * Prepare CRUD methods for working under ORM class \entities\AclMobileUser
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminMobUsers extends clsAdminEntity
{

    /**
     * self object
     * @var clsAdminRoles
     */
    private static $instance = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminUsers
     */
    public static function getInstance()
    {
        if( self::$instance == NULL ){
            self::$instance = new clsAdminMobUsers();
        }
        return self::$instance;
    }

    /**
     * Constructorof the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage( 'mobuser', ADMIN_ENTITIES_BLOCK );
        $this->em = clsDB::getInstance();
    }

    /**
     * Get list of mobile users
     *
     * @return array
     */
    public function getMobUsersList()
    {
        $query = $this->em->createQuery( "SELECT u FROM entities\AclMobileUser u" );
        return $query->getResult( Query::HYDRATE_SCALAR );
    }

    /**
     * Get Mob user data by ID
     * @param integer $id
     * mob user id
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getMobUserById( $id )
    {
        return $this->em->getRepository( 'entities\AclMobileUser' )->find( clsCommon::isInt( $id ) );
    }

    /**
     * Authorization - get mob user data by login & pass
     * @param string $login
     * login of the mob user
     * @param string $pass
     * password of the mob user
     * @return bool|mixed
     */
    public function getMobUserDataByAuth( $email, $pass )
    {
        $res = false;

        if( !empty( $email ) && !empty( $pass ) ){
            $email = htmlspecialchars( $email );
            $qb = $this->em->createQueryBuilder();
            $qb->select( 'u' )->from( 'entities\AclMobileUser', 'u' )->where( 'ad.email = :login' )->setParameter( 'login', $email )->leftJoin( 'entities\AclMobileRole', 'r', Join::WITH, $qb->expr()->eq( 'u.role', 'r.id' ) )->leftJoin( 'entities\AclMobilePermissionrole', 'rp', Join::WITH, $qb->expr()->eq( 'u.role', 'rp.roleId' ) )->leftJoin( 'entities\AclMobilePermission', 'p', Join::WITH, $qb->expr()->eq( 'rp.permId', 'p.id' ) )->setMaxResults( 1 );
            $res = $qb->getQuery()->getOneOrNullResult();
            if( $res ){
                if( !password_verify( $pass, $res->getPassword() ) ){
                    $res = false;
                }
            }
        }

        return $res;
    }

    /**
     * Update mob user data
     * @param integer $id
     * identificator of the mob user
     * @param string $login
     * login of the mob user
     * @param string $password
     * password of the mob user. Optional.
     * @return boolean
     * true - successfuly saved changes in the administrator profile, false - we have some errors
     */
    public function updateMobUser( $id, $email, $password = '', $role )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 && !empty( $email ) && clsCommon::isInt( $role ) > 0 ){
            $db = $this->em->createQueryBuilder();
            $db->select('u')->from('entities\AclMobileUser', 'u')
            ->where('u.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
            ->andWhere('u.email = :login')->setParameter('login', $email);
            $_mobuser = $db->getQuery()->getResult();
            if( $_mobuser ){
                $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $email ) );
                $this->errors->setError( $error, 1, false, true );
            }else{
                $mobuser = $this->em->getRepository( 'entities\AclMobileUser' )->find( clsCommon::isInt( $id ) );
                $mobuser->setEmail( $email );
                if( !empty( $password ) ){
                    $pass = $this->hashAdminPassword( $password );
                    $mobuser->setPassword( $pass );
                }
                $role = $this->em->getRepository( 'entities\AclMobileRole' )->find( clsCommon::isInt( $role ) );
                $mobuser->setRole( $role );

                $this->em->persist( $mobuser );
                $this->em->flush();
                $res = true;
            }
        }

        return $res;
    }

    /**
     * Delete mobile user
     * @param integer $id
     * @return boolean
     */
    public function deleteMobUser( $id )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 ){
            $mobuser = $this->em->getRepository( 'entities\AclMobileUser' )->find( clsCommon::isInt( $id ) );
            $this->em->remove( $mobuser );
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Add mobile user
     * @param string $email
     * email of the mobile user
     * @param string $password
     * passwotrd of the mobile user
     * @param integer $role
     * role ID of the mobile user
     * @return boolean
     */
    public function addMobUser( $email, $password, $role )
    {
        $res = false;
        if( !empty( $email ) && !empty( $password ) && clsCommon::isInt( $role ) > 0 ){
            $_mobuser = $this->em->getRepository( 'entities\AclMobileUser' )->findBy( array( 'email' => $email ) );
            if( $_mobuser ){
                $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $email ) );
                $this->errors->setError( $error, 1, false, true );
            }else{
                $mobuser = new AclMobileUser();
                $mobuser->setEmail( $email );
                $mobuser->setPassword( $this->hashAdminPassword( $password ) );
                $role = $this->em->getRepository( 'entities\AclMobileRole' )->find( clsCommon::isInt( $role ) );
                $mobuser->setRole( $role );
                $this->em->persist( $mobuser );
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Hash mobile user password
     * @param string $password
     * clear password
     * @return string
     */
    private function hashAdminPassword( $password )
    {
        return password_hash( $password, PASSWORD_BCRYPT, array( "cost" => 10 ) );
    }
}