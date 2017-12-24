<?php
namespace classes;

use engine\clsAdminEntity;

use classes\core\clsCommon;

use engine\modules\admin\clsAdminCommon;

use classes\core\clsDB;

/**
 * Prepare CRUD methods for working under ORM class \entities\Country
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminCountries extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminCountries $instance
     */
    private static $instance = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminCountries
     */
    public static function getInstance()
    {
        if( self::$instance == NULL ){
            self::$instance = new clsAdminCountries();
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
        $this->entity = clsAdminCommon::getAdminMessage( 'country', ADMIN_ENTITIES_BLOCK );
        $this->em = clsDB::getInstance();
    }

    /**
     * Get countries list
     * @param int $page
     * page number
     * @param int $limit
     * limit elements per page
     * @return array
     */
    public function getCountriesList($page = 1, $limit = DEF_PAGING_NUM)
    {
        $db = $this->em->createQueryBuilder();
        $db->select('c')->from('entities\Country', 'c');
        $db->setFirstResult(((int) $page - 1) * $limit);
        $db->setMaxResults((int) $limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the countries list
     */
    public function getCountriesListCount()
    {
        $query = $this->em->createQuery("SELECT COUNT(c) FROM \entities\Country c");
        return $query->getSingleScalarResult();
    }

    /**
     * Get country by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getCountryById( $id )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 ){
            $res = $this->em->getRepository( 'entities\Country' )->find( clsCommon::isInt( $id ) );
        }
        return $res;
    }

    /**
     * Add Country
     * @param string $name
     * name of the Country
     * @param string $img
     * path to file
     * @return boolean
     */
    public function addCountry( $name, $img = '' )
    {
        $res = false;
        if( !empty( $name ) ){

            $_country = $this->em->getRepository( 'entities\Country' )->findBy( array( 'name' => $name ) );
            if( $_country ){
                $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $name ) );
                $this->errors->setError( $error, 1, false, true );
            }else{
                $country = new \entities\Country();
                $country->setName( $name );
                if(!empty($img)){
                    $country->setImg($img);
                }
                $this->em->persist( $country );
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Update Country
     * @param int $id
     * identificator of the Country
     * @param string $name
     * name of the Country
     * @param string $img
     * path to file
     * @return boolean
     */
    public function updateCountry( $id, $name, $img = '' )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 && !empty( $name ) ){
            $db = $this->em->createQueryBuilder();
            $db->select('c')->from('entities\Country', 'c')
            ->where('c.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
            ->andWhere('c.name = :name')->setParameter('name', $name);
            $_country = $db->getQuery()->getResult();
            if( $_country ){
                $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $name ) );
                $this->errors->setError( $error, 1, false, true );
            }else{
                $country = $this->em->getRepository( 'entities\Country' )->find( clsCommon::isInt( $id ) );
                $country->setName( $name );
                if(!empty($img)){
                    $country->setImg($img);
                }
                $this->em->persist( $country );
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * delete Country
     * @param int $id
     * identificator of the Country
     * @return boolean
     */
    public function deleteCountry( $id )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 ){
            $role = $this->em->getRepository( 'entities\Country' )->find( clsCommon::isInt( $id ) );
            $this->em->remove( $role );
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}