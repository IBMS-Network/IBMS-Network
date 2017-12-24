<?php
namespace classes;

use engine\clsAdminEntity;

use classes\core\clsCommon;

use engine\modules\admin\clsAdminCommon;

use classes\core\clsDB;

/**
 * Prepare CRUD methods for working under ORM class \entities\AclMobilePermission
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminMobilePermissions extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminMobilePermissions
     */
    private static $instance = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminMobilePermissions
     */
    public static function getInstance()
    {
        if( self::$instance == NULL ){
            self::$instance = new clsAdminMobilePermissions();
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
        $this->entity = clsAdminCommon::getAdminMessage( 'mobpermission', ADMIN_ENTITIES_BLOCK );
        $this->em = clsDB::getInstance();
    }

    /**
     * Get mobile permissions list
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getPermissionsList()
    {
        return $this->em->getRepository( 'entities\AclMobilePermission' )->findAll();
    }

    /**
     * Get mobile permission by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getPermissionById( $id )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 ){
            $res = $this->em->getRepository( 'entities\AclMobilePermission' )->find( clsCommon::isInt( $id ) );
        }
        return $res;
    }

    /**
     * Add mobile Permission
     * @param string $name
     * name of the Permission
     * @return boolean
     */
    public function addPermission( $name )
    {
        $res = false;
        if( !empty( $name ) ){

            $_perm = $this->em->getRepository( 'entities\AclMobilePermission' )->findBy( array( 'name' => $name ) );
            if( $_perm ){
                $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $name ) );
                $this->errors->setError( $error, 1, false, true );
            }else{
                $perm = new \entities\AclMobilePermission();
                $perm->setName( $name );
                $this->em->persist( $perm );
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Update mobile Permission
     * @param int $id
     * identificator of the mobile Permission
     * @param string $name
     * name of the mobile Permission
     * @return boolean
     */
    public function updatePermission( $id, $name )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 && !empty( $name ) ){
            $db = $this->em->createQueryBuilder();
            $db->select('aclp')->from('entities\AclMobilePermission', 'aclp')
            ->where('aclp.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
            ->andWhere('aclp.name = :name')->setParameter('name', $name);
            $_perm = $db->getQuery()->getResult();
            if( $_perm ){
                $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $name ) );
                $this->errors->setError( $error, 1, false, true );
            }else{
                $perm = $this->em->getRepository( 'entities\AclMobilePermission' )->find( clsCommon::isInt( $id ) );
                $perm->setName( $name );

                $this->em->persist( $perm );
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * delete mobile Permission
     * @param int $id
     * identificator of the mobile Permission
     * @return boolean
     */
    public function deletePermission( $id )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 ){
            $role = $this->em->getRepository( 'entities\AclMobilePermission' )->find( clsCommon::isInt( $id ) );
            $this->em->remove( $role );
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}