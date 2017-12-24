<?php

namespace engine;

use engine\clsSysCommon;
use classes\core\clsCommon;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\ArrayCache as arraycache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Annotations\AnnotationReader as annreader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Gedmo\Timestampable\TimestampableListener;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Event\Listeners\MysqlSessionInit;
use Doctrine\ORM\Configuration;

class clsSysDB
{
    /**
     * @todo tmp var
     */
    static private $instance;

    /**
     * @var array Array with doctrine entity manager instances
     * Used for correcnt manage of multiple connections on system
     */
    static private $instances = array();

    /**
     * Method to get Doctrine Entity Manager instance by instance name
     *
     * @param string $instanceName
     * @return \Doctrine\ORM\EntityManager
     * @throws \Exception
     */
    public static function getInstance($instanceName = 'default')
    {
        /**
         * @todo tmp code
         */
        if (DB_DOCTRINE) {
            $instanceName = strtolower($instanceName);

            if (empty(self::$instances[$instanceName])) {
                $domainConfig = clsCommon::getDomainConfig();
                if (empty($domainConfig['DB'])) {
                    if (clsSysCommon::getCommonDebug()) {
                        $error_message = clsSysCommon::getMessage('db_config_incorrect', 'Errors');
                        throw new \Exception($error_message);
                    }
                }
                try {
                    // db
                    // Second configure ORM
// globally used cache driver, in production use APC or memcached
                    $cache = new arraycache;
//                    $memcache = new \Memcache();
//                    $memcache->connect('localhost', 11211);

//                    $cache = new MemcacheCache();
//                    $cache->setMemcache($memcache);
// standard annotation reader
                    $annotationReader = new annreader;
                    $cachedAnnotationReader = new CachedReader(
                        $annotationReader, // use reader
                        $cache // and a cache driver
                    );
// create a driver chain for metadata reading
                    $driverChain = new MappingDriverChain();
// load superclass metadata mapping only, into driver chain
// also registers Gedmo annotations.NOTE: you can personalize it
                    \Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
                        $driverChain, // our metadata driver chain, to hook into
                        $cachedAnnotationReader // our cached annotation reader
                    );

                    $paths = array(MODELS_PATH . DIRECTORY_SEPARATOR . 'entities');
// now we want to register our application entities,
// for that we need another metadata driver used for Entity namespace
                    $annotationDriver = new AnnotationDriver(
                        $cachedAnnotationReader, // our cached annotation reader
                        array($paths) // paths to look in
                    );
// NOTE: driver for application Entity can be different, Yaml, Xml or whatever
// register annotation driver for our application Entity namespace
                    $driverChain->addDriver($annotationDriver, 'Entity');

                    $isDevMode = (!empty($domainConfig['DB']['is_dev_mode'])) ? $domainConfig['DB']['is_dev_mode'] : 'true';
                    $proxiesDir = (constant('DOCTRINE_PROXIES_DIR')) ? DOCTRINE_PROXIES_DIR : null;
                    $config = Setup::createAnnotationMetadataConfiguration(
                        $paths,
                        $isDevMode,
                        $proxiesDir,
                        null,
                        false
                    );
                    $config->addCustomNumericFunction(
                        'GEO_DISTANCE',
                        'Craue\GeoBundle\Doctrine\Query\Mysql\GeoDistance'
                    );
                    $domainConfig['DB']['charset'] = 'utf8';

                    $evm = new EventManager();
                    // sluggable
                    $sluggableListener = new \Gedmo\Sluggable\SluggableListener;
// you should set the used annotation reader to listener, to avoid creating new one for mapping drivers
                    $sluggableListener->setAnnotationReader($cachedAnnotationReader);
                    $evm->addEventSubscriber($sluggableListener);

// tree
                    $treeListener = new \Gedmo\Tree\TreeListener;
                    $treeListener->setAnnotationReader($cachedAnnotationReader);
                    $evm->addEventSubscriber($treeListener);

// timestampable
                    $timestampableListener = new \Gedmo\Timestampable\TimestampableListener;
                    $timestampableListener->setAnnotationReader($cachedAnnotationReader);
                    $evm->addEventSubscriber($timestampableListener);

                    $evm->addEventSubscriber(new MysqlSessionInit());

                    $config->addCustomStringFunction('rand', 'Mapado\MysqlDoctrineFunctions\DQL\MysqlRand');
                    $config->addCustomStringFunction('round', 'Mapado\MysqlDoctrineFunctions\DQL\MysqlRound');
                    $config->addCustomStringFunction('date', 'Mapado\MysqlDoctrineFunctions\DQL\MysqlDate');
                    $config->addCustomStringFunction('date_format', 'Mapado\MysqlDoctrineFunctions\DQL\MysqlDateFormat');

                    
                    self::$instances[$instanceName] = EntityManager::create($domainConfig['DB'], $config, $evm);
                    

                    return self::$instances[$instanceName];
                } catch (\Exception $e) {
                    if (clsSysCommon::getCommonDebug()) {
                        $error_message = clsSysCommon::getMessage('system_down', 'Errors');
                        throw new \Exception($error_message);
                    }
                }
            }

            return self::$instances[$instanceName];
        } else {
            // loading adodb
            require_once(CORE_3RDPARTY_PATH . 'adodb/adodb-exceptions.inc.php');
            require_once(CORE_3RDPARTY_PATH . 'adodb/adodb.inc.php');
            if (self::$instance == NULL) {
                self::$instance =& NewADOConnection('mysql');

                try {
                    $config = clsCommon::getDomainConfig();
                    self::$instance->Connect($config['Domain']['DB_HOST'], $config['Domain']['DB_USER'], $config['Domain']['DB_PASSWORD'], $config['Domain']['DB_NAME']);
                } catch (exception $e) {
                    if (USE_DEBUG) {
                        exit($e->getMessage() . "<br />laga");
                    }
                }
                self::$instance->debug = USE_DEBUG_SQL;
                self::$instance->raiseErrorFn = "myError";
                self::$instance->SetFetchMode(ADODB_FETCH_ASSOC);
                self::$instance->Execute('SET NAMES utf8');
            }

            return self::$instance;
        }

    }

    public static function myError($datatype, $func_type = "", $errnum = "", $errmsg = "", $sql = "", $inputarr = "", $class = "")
    {
        $error = "MYSQL ERROR : " . $errmsg . "<br /> SQL : " . $sql . "<br /> FILE : " . __FILE__ . " LINE : " . __LINE__;
        if (USE_ERROR_LOG) {
            clsCommon::Log($error, 3, "mysql_log.log");
        };
        if (USE_DEBUG_SQL) {
            if (defined('API_MODE')) {
                throw new \Exception($error, API_ERROR_CODE_ERROR);
            } else {
                die($error);
            }
        } else {
            if (defined('API_MODE')) {
                throw new \Exception($error, API_ERROR_CODE_ERROR);
            } else {
                clsCommon::redirect302("Location: /Warning/?err_num=" . DEF_SQL_QUERY_ERR);
                die('Error on Connect');
            }
        }
    }

    protected function fetchArr($arr)
    {
        $myarr = array();
        while ($result = mysql_fetch_assoc($arr)) {
            $myarr[] = $result;
        }

        return $myarr;
    }

    public function show($arr, $is = false, $color = "red")
    {
        if ($is)
            echo "<div style='color:" . $color . "'>";
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
        if ($is) {
            echo "</div>";
            $this->clearError();
        }
    }

    protected function setError($error)
    {
        $this->errors[] = $error;
    }

    protected function clearError()
    {
        $this->errors = array();
    }

    public function isError()
    {
        if (!empty($this->errors) && count($this->errors) > 0)
            return true;

        return false;
    }

    protected function setEmptyFields($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }

}