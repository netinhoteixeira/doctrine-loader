<?php

use Doctrine\Common\ClassLoader;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;

/**
 * Class DoctrineLoader200.
 */
class DoctrineLoader200 {

    /**
     * Instance of this class.
     *
     * @var DoctrineLoader200
     */
    protected static $instance;
    private $config;
    private $connectionParams;
    private $evm;
    private $em;

    /**
     * Constructor.
     */
    function __construct() {
        // Setup Autoloader (1)
        // See :doc:`Configuration <../reference/configuration>` for up to date autoloading details.
        $indebug = defined('WP_DEBUG') ? WP_DEBUG : false;

        // Doctrine uses a class loader to autoload the required classes
        // http://mackstar.com/blog/2010/07/29/doctrine-2-and-why-you-should-use-it
        require_once DOCTRINE_LIBRARY_DIR . '/Doctrine/Common/ClassLoader.php';

        // Lets first load the Doctrine library
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine', DOCTRINE_LIBRARY_DIR);
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('Common', DOCTRINE_LIBRARY_DIR . '/Doctrine');
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('DBAL', DOCTRINE_LIBRARY_DIR . '/Doctrine');
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('ORM', DOCTRINE_LIBRARY_DIR . '/Doctrine');
        $classLoader->register();

        // This allows Doctrine-CLI tool & YAML mapping driver
        $classLoader = new \Doctrine\Common\ClassLoader('Symfony', DOCTRINE_LIBRARY_DIR . '/Doctrine');
        $classLoader->register();

        // Provide some initial database information
        switch (DB_TYPE) {
            case 'sqlite':
                $this->connectionParams = array(
                    'driver' => 'pdo_' . DB_TYPE,
                    'path' => DB_NAME
                );
                break;

            case 'mysql':
                $this->connectionParams = array(
                    'driver' => 'pdo_' . DB_TYPE,
                    'user' => DB_USER,
                    'password' => DB_PASSWORD,
                    'host' => DB_HOST,
                    'dbname' => DB_NAME,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'' . DB_CHARSET . '\''
                );
                break;
        }

        self::loadmodels();

        // Load entities
        $classLoader = new \Doctrine\Common\ClassLoader('entities', DOCTRINE_OUTPUT_DIR);
        $classLoader->register();

        // detect if folder of proxies exists and make if not
        if (!is_dir(DOCTRINE_PROXIES_DIR)) {
            mkdir(DOCTRINE_PROXIES_DIR, 0775, true);
        }

        // Load proxies
        $classLoader = new \Doctrine\Common\ClassLoader('proxies', DOCTRINE_OUTPUT_DIR);
        $classLoader->register();

        // Provide some initial database information
        $this->config = new \Doctrine\ORM\Configuration(); // (2)
        // Proxy Configuration (3)
        $this->config->setProxyDir(DOCTRINE_PROXIES_DIR);
        $this->config->setProxyNamespace('Proxies');
        $this->config->setAutoGenerateProxyClasses($indebug);

        // Mapping Configuration (4)
        $this->config->setMetadataDriverImpl($this->config->newDefaultAnnotationDriver(DOCTRINE_ENTITIES_DIR));

        if ($indebug) {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        } else {
            $cache = new \Doctrine\Common\Cache\ApcCache();
        }
        $this->config->setMetadataCacheImpl($cache);
        $this->config->setQueryCacheImpl($cache);

        // obtaining the entity manager (7)
        $this->evm = new Doctrine\Common\EventManager();
        $this->em = \Doctrine\ORM\EntityManager::create($this->connectionParams, $this->config, $this->evm);
    }

    /**
     * Get or create a instance object of current class.
     *
     * @return DoctrineLoader200
     */
    final public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Generate and load all database models
     */
    function loadmodels() {
        // detect if folder of entities exists and make if not
        if (!is_dir(DOCTRINE_ENTITIES_DIR)) {
            mkdir(DOCTRINE_ENTITIES_DIR, 0775, true);
        }

        // detect if entities exists and generate if not
        if (count_files_in_dir(DOCTRINE_ENTITIES_DIR) . '/*.php') {
            $config = new \Doctrine\ORM\Configuration();
            $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(DOCTRINE_ENTITIES_DIR));
            $config->setProxyDir(DOCTRINE_PROXIES_DIR);
            $config->setProxyNamespace('Proxies');
            $em = \Doctrine\ORM\EntityManager::create($this->connectionParams, $config);
            // custom datatypes (not mapped for reverse engineering)
            $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('set', 'string');
            $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            $em->getConfiguration()->setMetadataDriverImpl(
                    new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
                            $em->getConnection()->getSchemaManager()
                    )
            );

            $cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
            $cmf->setEntityManager($em);

            $generator = new EntityGenerator();
            $generator->setUpdateEntityIfExists(true);
            $generator->setGenerateStubMethods(true);
            $generator->setGenerateAnnotations(true);
            $generator->generate($cmf->getAllMetadata(), DOCTRINE_ENTITIES_DIR);
        }
    }

    public function getEntityManager() {
        return $this->em;
    }

}

?>