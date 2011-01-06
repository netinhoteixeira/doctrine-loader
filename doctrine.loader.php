<?php

/*
  // constants of connection
  define('DB_TYPE', 'mysql');
  define('DB_USER', 'root');
  define('DB_PASSWORD', '');
  define('DB_HOST', '127.0.0.1');
  define('DB_NAME', 'estatistica');
  define('DB_CHARSET', 'utf8');

  define('DB_TYPE', 'sqlite');
  define('DB_NAME', 'estatistica.sqlite');

  define('WP_DEBUG', true);
 */
define('DOCTRINE_LOADER_VERSION', '0.1');
if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}
if (!defined('DOCTRINE_NEWEST_PHP_VERSION')) {
    define('DOCTRINE_NEWEST_PHP_VERSION', '5.3.0');
}
if (!defined('DOCTRINE_DAO_DIR')) {
    define('DOCTRINE_DAO_DIR', __DIR__ . '/../../dao/doctrine');
}
//require_once dirname(__FILE__) . '/functions/deltree.func.php';
require_once dirname(__FILE__) . '/functions/count_files_in_dir.func.php';
require_once dirname(__FILE__) . '/functions/php_compatible_version_do.func.php';

$GLOBALS['doctrine'] = null;

php_compatible_version_do('doctrine_loader_doctrine200', 'doctrine_loader_doctrine123', DOCTRINE_NEWEST_PHP_VERSION);

function doctrine_loader_doctrine200() {
    define('DOCTRINE_VERSION', '2.0.0');
    define('DOCTRINE_LIBRARY_DIR', __DIR__ . '/doctrine/' . DOCTRINE_VERSION);
    if (!defined('DOCTRINE_OUTPUT_DIR')) {
        define('DOCTRINE_OUTPUT_DIR', DOCTRINE_DAO_DIR . '/' . DOCTRINE_VERSION);
    }
    if (!defined('DOCTRINE_ENTITIES_DIR')) {
        define('DOCTRINE_ENTITIES_DIR', DOCTRINE_OUTPUT_DIR . '/entities');
    }
    if (!defined('DOCTRINE_PROXIES_DIR')) {
        define('DOCTRINE_PROXIES_DIR', DOCTRINE_OUTPUT_DIR . '/proxies');
    }

    require_once __DIR__ . '/doctrine.loader-' . DOCTRINE_VERSION . '.php';

    $GLOBALS['doctrine'] = DoctrineLoader200::getInstance();
}

function doctrine_loader_doctrine123() {
    define('DOCTRINE_VERSION', '1.2.3');
    if (!defined('DOCTRINE_DSN')) {
        switch (DB_TYPE) {
            case 'sqlite':
                define('DOCTRINE_DSN', DB_TYPE . ':///' . DB_NAME);
                break;

            case 'mysql':
                define('DOCTRINE_DSN', DB_TYPE . '://' . DB_USER . ':' . DB_PASSWORD . '@' . DB_HOST . '/' . DB_NAME);
                break;
        }
    }
    define('DOCTRINE_LIBRARY_DIR', __DIR__ . '/doctrine/' . DOCTRINE_VERSION);
    if (!defined('DOCTRINE_OUTPUT_DIR')) {
        define('DOCTRINE_OUTPUT_DIR', DOCTRINE_DAO_DIR . '/' . DOCTRINE_VERSION);
    }
    if (!defined('DOCTRINE_MODELS_DIR')) {
        define('DOCTRINE_MODELS_DIR', DOCTRINE_OUTPUT_DIR . '/models');
    }
    if (!defined('DOCTRINE_SHORTCODES_DIR')) {
        define('DOCTRINE_SHORTCODES_DIR', DOCTRINE_OUTPUT_DIR . '/shortcodes');
    }

    require_once __DIR__ . '/doctrine.loader-' . DOCTRINE_VERSION . '.php';

    $GLOBALS['doctrine'] = DoctrineLoader123::getInstance();
}

?>