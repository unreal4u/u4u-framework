<?php

define ( 'MEMORY_LIMIT', '128M' );
define ( 'APP_ENVIRONMENT' , 'dev');

define ( 'PUBLICFOLDERNAME', 'www');
define ( 'HOME', 'http://localhost/framework/'.PUBLICFOLDERNAME.'/' );
define ( 'ROUT', dirname(__FILE__).'/' );
define ( 'PUBLICROOT', ROUT.'/../'.PUBLICFOLDERNAME.'/');
define ( 'HOMEPAGE', 'index/index/' ); // module / controller
define ( 'CLASSES', ROUT . 'classes/framework/' );
define ( 'THIRDPARTY_DIRECTORY' , ROUT. 'classes/thirdparty/' );
define ( 'USER_SPACE', ROUT.'user/');
define ( 'IMAG', 'im/' );
define ( 'JSCR', 'js/' );
define ( 'CONTROLLERS', USER_SPACE . 'modules/' );
define ( 'PART', CONTROLLERS . 'parts/' );
define ( 'LOCALE_DIR', USER_SPACE.'locale/');

define ( 'SESION_EXPIRE', 3600 );
define ( 'CACHE_EXPIRE', 3600 );
define ( 'SESION_NAME', 'framework-sesion' );
define ( 'SESION_ROUT', ROUT . 'sessions/' );
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
define ( 'DB_MYSQLI_PASS', 'holamundo' ); // your db's password
define ( 'DB_MYSQLI_NAME', 'framework' ); // your db's database name
define ( 'DB_MYSQLI_CHAR', 'utf8' ); // The DB's charset

define ( 'LOGLEVEL', 15 );
define ( 'LOGLOCATION', '/home/sgp/logs-sistema/' );
define ( 'LOG_AJAX_REQUESTS', FALSE );
