<?php

namespace configuration;

/**
 * Basic model of all the configuration options. Will hold defaults
 *
 * All the options here should be overwritten in your user space configurations folder
 *
 * @author unreal4u
 */
abstract class baseConfiguration {
    /**
     * Holds all the base configuration
     * @var array
     */
    private $_baseConfig = array();

    /**
     * Holds the final configuration
     * @var array
     */
    private $_finalConfiguration = array();

    public function __construct() {
        $this->loadBaseConfiguration();
    }

    public function __destruct() {
        // TODO
    }

    /**
     * Creates the finalConfiguration array
     *
     * @param array $options
     */
    public function mergeConfiguration($options) {
        foreach ($this->_baseConfig as $key => $value) {
            if (isset($options[$key])) {
                $value = $options[$key];
            }

            $this->_finalConfiguration[$key] = $value;
            unset($this->_baseConfig[$key]);
        }
    }

    /**
     * Loads the base class options
     */
    private function loadBaseConfiguration() {
        // The very basics
        $this->_baseConfig['MEMORY_LIMIT'] = '8M';
        $this->_baseConfig['APP_ENVIRONMENT'] = 'dev';
        $this->_baseConfig['PUBLIC_FOLDERNAME'] = 'www';
        $this->_baseConfig['REWRITE_BASE'] = '/u4u-framework/'.$this->_baseConfig['PUBLIC_FOLDERNAME'] . '/';

        // More basic stuff, related mainly with paths
        $this->_baseConfig['HOME'] = 'http://localhost'.$this->_baseConfig['REWRITE_BASE'];
        $this->_baseConfig['ABSPATH'] = rtrim(realpath(rtrim(dirname(__FILE__), '/').'/../../'), '/').'/';
        $this->_baseConfig['HOMEPAGE'] = 'index/index/'; // module / controller
        $this->_baseConfig['CLASSES'] = $this->_baseConfig['ABSPATH'].'classes/framework/';
        $this->_baseConfig['THIRDPARTY_DIRECTORY'] = $this->_baseConfig['ABSPATH'].'classes/thirdparty/';
        $this->_baseConfig['USER_SPACE'] = $this->_baseConfig['ABSPATH'].'user/';
        $this->_baseConfig['CONTROLLERS'] = $this->_baseConfig['USER_SPACE'].'modules/';
        $this->_baseConfig['LOCALE_DIR'] = $this->_baseConfig['USER_SPACE'].'locale/';

        // Public section
        $this->_baseConfig['PUBLICROOT'] = rtrim(realpath($this->_baseConfig['ABSPATH'].'../'.$this->_baseConfig['PUBLIC_FOLDERNAME']), '/').'/';
        $this->_baseConfig['IMAG'] = 'im/';
        $this->_baseConfig['JSCR'] = 'js/';

        // Security and cache related settings
        $this->_baseConfig['SESION_EXPIRE'] = 3600;
        $this->_baseConfig['CACHE_EXPIRE'] = 7200;
        $this->_baseConfig['SESSION_NAME'] = 'framework-sesion';
        $this->_baseConfig['SESSION_PATH'] = $this->_baseConfig['ABSPATH'].'cache/sessions/';
        $this->_baseConfig['PASSWD_HASH'] = 'j1tWLoyCxy244I9MvZ5Jgh1sAWpV8Dfd';

        // Classes related stuff (Should soon disappear)
        $this->_baseConfig['CHARSET'] = 'UTF-8'; // The charset to use
        $this->_baseConfig['OPTIMIZE_CSS'] = true; // Whether to strip the most common byte-eaters
        $this->_baseConfig['USE_CSS_CACHE'] = true; // Whether to use internal cache
        $this->_baseConfig['GZIP_CONTENTS'] = true; // Use TRUE only when the server doesn't compress CSS natively
        $this->_baseConfig['GZIP_LEVEL'] = 6; // GZIP compression level, range from 1 to 9
        $this->_baseConfig['CACHE_LOCATION'] = $this->_baseConfig['PUBLICROOT'].'cache/';// Cache location, WITH trailing slash, should be writable
        $this->_baseConfig['USE_BROWSER_CACHE'] = true; // Whether to instruct the browser to save the CSS in cache
        $this->_baseConfig['TIME_BROWSER_CACHE'] = '3600'; // Time in seconds the browser caches our CSS
        $this->_baseConfig['EXTERNAL_ROUTE'] = 'cache/';

        // Database related stuff
        $this->_baseConfig['DB_MYSQLI_HOST'] = 'localhost'; // your db's host
        $this->_baseConfig['DB_MYSQLI_PORT'] = 3306;        // your db's port
        $this->_baseConfig['DB_MYSQLI_USER'] = 'framework'; // your db's username
        $this->_baseConfig['DB_MYSQLI_PASS'] = 'framework'; // your db's password
        $this->_baseConfig['DB_MYSQLI_NAME'] = 'framework'; // your db's database name
        $this->_baseConfig['DB_MYSQLI_CHAR'] = 'utf8';      // The DB's charset

        // Log related stuff
        $this->_baseConfig['LOGLEVEL'] = 15;
        $this->_baseConfig['LOGLOCATION'] = $this->_baseConfig['ABSPATH'].'cache/logs/';
        $this->_baseConfig['LOG_AJAX_REQUESTS'] = false;
    }

    /**
     * Converts the finalConfiguration array into system wide options constant
     *
     * @return boolean
     */
    public function convertToConstants() {
        foreach ($this->_finalConfiguration as $key => $value) {
            define($key, $value);
            unset($this->_finalConfiguration[$key]);
        }

        return true;
    }
}