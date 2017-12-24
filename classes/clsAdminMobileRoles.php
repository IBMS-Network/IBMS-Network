<?php
namespace classes;

use engine\clsAdminEntity;

use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use classes\core\clsDB;

/**
 * Prepare CRUD methods for working under ORM class \entities\AclMobileRole
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminMobileRoles extends clsAdminEntity
{

    /**
     * self object
     * @var clsAdminMobileRoles
     */
    private static $instance = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminMobileRoles
     */
    public static function getInstance()
    {
        if( self::$instance == NULL ){
            self::$instance = new clsAdminMobileRoles();
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
        $this->em = clsDB::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage( 'mobrole', ADMIN_ENTITIES_BLOCK );
    }

    /**
     * Get mobile roles list
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRolesList()
    {
        return $this->em->getRepository('entities\AclMobileRole')->findBy([], ['id' => 'DESC']);
    }

    /**
     * Get Mobile Role data by ID
     * @param int $id
     * identificator of the mobile Role
     * @return boolean | \Doctrine\ORM\EntityRepository
     */
    public function getRoleById( $id )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 ){
            $res = $this->em->getRepository( '\entities\AclMobileRole' )->find( clsCommon::isInt( $id ) );
        }
        return $res;
    }

    /**
     * Update mobile role
     * @param int $id
     * identificator of the mobile Role
     * @param string $name
     * name of the mobile Role
     * @return boolean
     */
    public function updateRole( $id, $name, $perm )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 && !empty( $name ) ){
            $db = $this->em->createQueryBuilder();
            $db->select('aclr')->from('entities\AclMobileRole', 'aclr')
            ->where('aclr.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
            ->andWhere('aclr.name = :name')->setParameter('name', $name);
            $_role = $db->getQuery()->getResult();
            if( $_role ){
                $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $name ) );
                $this->errors->setError( $error, 1, false, true );
            }else{
                $role = $this->em->getRepository( '\entities\AclMobileRole' )->find( clsCommon::isInt( $id ) );
                $role->setName( $name );
                $permsIds = array_keys( $perm );
                array_walk( $permsIds, function ( &$val )
                {
                    $val = clsCommon::isInt( $val );
                } );
                $perms = '';
                $perms = $this->em->getRepository('\entities\AclMobilePermission')->findBy(['id' => $permsIds]);
                $role->setPermissions( $perms );
                $this->em->persist( $role );
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Delete mobile Role
     * @param int $id
     * identificator of the mobile Role
     * @return boolean
     */
    public function deleteRole( $id )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 ){
            $role = $this->em->getRepository( '\entities\AclMobileRole' )->find( clsCommon::isInt( $id ) );
            $this->em->remove( $role );
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Add mobile Role
     * @param string $name
     * name of the Role
     * @param array $perms
     * @return boolean | integer
     */
    public function addRole( $name, $perms )
    {
        $_role = $this->em->getRepository( 'entities\AclMobileRole' )->findBy( array( 'name' => $name ) );
        if( $_role ){
            $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $name ) );
            $this->errors->setError( $error, 1, false, true );
        }else{
            $permsIds = array_keys( $perms );
            array_walk( $permsIds, function ( &$val )
            {
                $val = clsCommon::isInt( $val );
            } );
            $perms = $this->em->getRepository('\entities\AclMobilePermission')->findBy(['id' => $permsIds]);
            $roleEntity = new \entities\AclMobileRole();
            $roleEntity->setName( $name );
            $roleEntity->setPermissions( $perms );
            $this->em->persist( $roleEntity );
            $this->em->flush();
            $res = $roleEntity->getId();
        }
        return $res;
    }
}