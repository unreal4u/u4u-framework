<?php

/*
 * Copy this file to config.php and then adjust the default values
 */

/**
 * The memory limit, defaults to 8M
 * @var string
 */
const MEMORY_LIMIT = '8M';

/**
 * Application environment, defaults to "dev". Can be changed to "production"
 * @var string
 */
const APP_ENVIRONMENT = 'dev';

/**
 * What the public_html folder is named. Defaults to "public_html"
 * @var string
 */
const PUBLICFOLDERNAME = 'www';

/**
 * If the site is loaded under a folder, write its name here
 * @var string
 */
define ( 'REWRITE_BASE' , '/u4u-framework/' . PUBLICFOLDERNAME . '/' );

/**
 * Absolute path of the root page
 * @var string
 */
define ( 'HOME', 'http://localhost'.REWRITE_BASE );

define ( 'ABSPATH', dirname(__FILE__).'/' );
define ( 'PUBLICROOT', ABSPATH.'../'.PUBLICFOLDERNAME.'/');
define ( 'HOMEPAGE', 'index/index/' ); // module / controller
define ( 'CLASSES', ABSPATH . 'classes/framework/' );
define ( 'THIRDPARTY_DIRECTORY' , ABSPATH. 'classes/thirdparty/' );
define ( 'USER_SPACE', ABSPATH.'user/');
define ( 'IMAG', 'im/' );
define ( 'JSCR', 'js/' );
define ( 'CONTROLLERS', USER_SPACE . 'modules/' );
define ( 'PART', CONTROLLERS . 'parts/' );
define ( 'LOCALE_DIR', USER_SPACE.'locale/');

define ( 'SESION_EXPIRE', 3600 );
define ( 'CACHE_EXPIRE', 7200 );
define ( 'SESSION_NAME', 'framework-sesion' );
define ( 'SESSION_PATH', ABSPATH . 'cache/sessions/' );
define ( 'PASSWD_HASH', 'j1tWLoyCxy244I9MvZ5Jgh1sAWpV8Dfd' );

define ( 'CHARSET', 'UTF-8' ); // The charset to use
define ( 'OPTIMIZE_CSS', TRUE ); // Whether to strip the most common byte-eaters
define ( 'USE_CSS_CACHE', TRUE ); // Whether to use internal cache
define ( 'GZIP_CONTENTS', TRUE ); // Use TRUE only when the server doesn't compress CSS natively
define ( 'GZIP_LEVEL', 6 ); // GZIP compression level, range from 1 to 9
define ( 'CACHE_LOCATION', PUBLICROOT.'cache/');// Cache location, WITH trailing slash, should be writable
define ( 'USE_BROWSER_CACHE', TRUE ); // Whether to instruct the browser to save the CSS in cache
define ( 'TIME_BROWSER_CACHE', '3600' ); // Time in seconds the browser caches our CSS
define ( 'EXTERNAL_ROUTE', 'cache/');

define ( 'DB_MYSQLI_HOST', 'localhost' ); // your db's host
define ( 'DB_MYSQLI_PORT', 3306 ); // your db's port
define ( 'DB_MYSQLI_USER', 'framework' ); // your db's username
define ( 'DB_MYSQLI_PASS', 'framework' ); // your db's password
define ( 'DB_MYSQLI_NAME', 'framework' ); // your db's database name
define ( 'DB_MYSQLI_CHAR', 'utf8' ); // The DB's charset

define ( 'LOGLEVEL', 15 );
define ( 'LOGLOCATION', '/home/sgp/logs-sistema/' );
define ( 'LOG_AJAX_REQUESTS', FALSE );




/*
 * class configuration {
    /**
     * This will hold all options
     * @var array
     *
    private $_options = array();

    public function __construct() {
        $this->_options = array(
            // The memory limit, defaults to 16M
            'MEMORY_LIMIT'         => '16M',
            // Application environment, defaults to "dev". Can be changed to "production"
            'APP_ENVIRONMENT'      => 'dev',
            // What the public_html folder is named. Defaults to "public_html"
            'PUBLICFOLDERNAME'     => 'www',
            // If the site is loaded under a folder, write its name here
            'REWRITE_BASE'         => '/u4u-framework/'.$this->_options['PUBLICFOLDERNAME'],
            // Absolute path of the root page
            'HOME'                 => 'http://localhost'.$this->_options['REWRITE_BASE'],
            // Which module / controller should be considered to be the home page
            'HOMEPAGE'             => 'index/index/',
            // Absolute path of the application
            'ABSPATH'              => dirname(__FILE__).'/',
            // Absolute path of the public html dir
            'PUBLICPATH'           => $this->_options['ABSPATH'].'../'.$this->_options['PUBLICFOLDERNAME'],
            // Absolute path of the classes directory
            'CLASSES'              => $this->_options['ABSPATH'].'classes/framework/',
            // Absolute path of the thirdparty directory
            'THIRDPARTY_DIRECTORY' => $this->_options['ABSPATH'].'classes/thirdparty/',
            // Absolute path of the user space
            'USER_SPACE'           => $this->_options['ABSPATH'].'user/',
            // Absolute path of the controllers
            'CONTROLLERS'          => $this->_options['USER_SPACE'].'modules/',
            // Absolute path of the locales directory
            'LOCALE_DIR'           => $this->_options['USER_SPACE'].'locale/',
            // Relative path (of public folder) of the images directory
            'IMAG'                 => 'im/',
            // Relative path (of public folder) of the js directory
            'JSCR'                 => 'js/',
            // Session expire time
            'SESION_EXPIRE'        => 3600,
            // Cache expire time
            'CACHE_EXPIRE'         => 3600,
            // Session name
            'SESION_NAME'          => 'framework-session',
            // File-based sessions should be stored in this absolute path
            'SESION_ROUT'          => $this->_options['ABSPATH'].'sessions/',
            // Password generator hash
            'PASSWD_HASH'          => 'j1tWLoyCxy244I9MvZ5Jgh1sAWpV8Dfd',

            // Cache location, WITH trailing slash, should be writable
            'CACHE_LOCATION'       => $this->_options['PUBLICPATH'].'cache/',
        );

        $this->_setConstants();
    }

    private function _setConstants() {
        foreach($this->_options AS $name => $value) {
            define('\\'.$name, $value);
        }
    }
}



new configuration();
 */