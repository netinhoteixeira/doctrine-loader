<?php

/**
 * Class DoctrineLoader.
 */
class DoctrineLoader {

    /**
     * Instance of this class.
     *
     * @var DoctrineLoader
     */
    protected static $instance;

    /**
     * Constructor.
     */
    function __construct() {

    }

    /**
     * Get or create a instance object of current class.
     *
     * @return DoctrineLoader
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

    }

}

?>