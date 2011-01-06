<?php

/**
 * Class DoctrineLoader123.
 */
class DoctrineLoader123 {

    /**
     * Instance of this class.
     *
     * @var DoctrineLoader123
     */
    protected static $instance;

    /**
     * Constructor.
     */
    function __construct() {
        // load Doctrine library
        require_once DOCTRINE_LIBRARY_DIR . '/Doctrine.php';

        // this will allow Doctrine to load Model classes automatically
        spl_autoload_register(array('Doctrine', 'autoload'));

        Doctrine_Manager::connection(DOCTRINE_DSN, 'default');

        self::loadmodels();

        // (OPTIONAL) CONFIGURATION BELOW
        // load our shortcodes
        if (is_dir(DOCTRINE_SHORTCODES_DIR)) {
            $doctrine_shortcodes_dir = glob(DOCTRINE_SHORTCODES_DIR . '/*.php');
            if ($doctrine_shortcodes_dir) {
                foreach ($doctrine_shortcodes_dir as $shortcode_file) {
                    require_once( $shortcode_file );
                }
            }
            unset($doctrine_shortcodes_dir);
        } else {
            mkdir(DOCTRINE_SHORTCODES_DIR, 0775, true);
        }

        // this will allow us to use "mutators"
        Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

        // this sets all table columns to notnull and unsigned (for ints) by default
        Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_DEFAULT_COLUMN_OPTIONS,
                array('notnull' => true, 'unsigned' => true));

        // set the default primary key to be named 'id', integer, 20 bytes as default MySQL bigint
        Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_DEFAULT_IDENTIFIER_OPTIONS,
                array('name' => 'id', 'type' => 'integer', 'length' => 20));

        if (defined('DB_CHARSET')) {
            Doctrine_Manager::getInstance()->setCharset(DB_CHARSET);
        }
    }

    /**
     * Get or create a instance object of current class.
     *
     * @return DoctrineLoader123
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
        // detect if model's folder exists and make if not
        if (!is_dir(DOCTRINE_MODELS_DIR)) {
            mkdir(DOCTRINE_MODELS_DIR, 0775, true);
        }

        // detect if models exists and generate if not
        if (count_files_in_dir(DOCTRINE_MODELS_DIR) . '/*.php') {
            Doctrine_Core::generateModelsFromDb(DOCTRINE_MODELS_DIR, array('default'),
                            array('generateTableClasses' => true));
        }

        // telling Doctrine where our models are located
        Doctrine::loadModels(DOCTRINE_MODELS_DIR . '/generated');
        Doctrine::loadModels(DOCTRINE_MODELS_DIR);
    }

}

?>