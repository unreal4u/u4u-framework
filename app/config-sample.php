<?php
$r['locale'] = setlocale (LC_ALL, 'es_ES', 'es_CL', 'es', 'ES' );
date_default_timezone_set ( 'America/Santiago' );

define ( 'MEMORY_LIMIT', '128M' );
define ( 'APP_ENVIRONMENT' , 'dev' );

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
define ( 'SESION_NOMBRE', 'framework-sesion' );
define ( 'SESION_ROUT', ROUT . 'sessions/' );
define ( 'PASSWD_HASH', 'j1tWLoyCxy244I9MvZ5Jgh1sAWpV8Dfd' );

if (APP_ENVIRONMENT === 'dev') $r ['q'] = $_SERVER ['REQUEST_TIME'] + microtime ();

define ( 'CHARSET', 'UTF-8' ); // The charset to use
define ( 'OPTIMIZE_CSS', TRUE ); // Whether to strip the most common byte-eaters
define ( 'USE_CSS_CACHE', TRUE ); // Whether to use internal cache
define ( 'GZIP_CONTENTS', TRUE ); // Use TRUE only when the server doesn't compress CSS natively
define ( 'GZIP_LEVEL', 6 ); // GZIP compression level, range from 1 to 9
define ( 'CACHE_LOCATION', PUBLICROOT.'cache/');// Cache location, WITH trailing slash, should be writable
define ( 'USE_BROWSER_CACHE', TRUE ); // Whether to instruct the browser to save the CSS in cache
define ( 'TIME_BROWSER_CACHE', '3600' ); // Time in seconds the browser caches our CSS

//echo CACHE_LOCATION;
define ( 'IMG_SUCCESS', 'im/verde.png' ); // Imagen a mostrarse en los avisos de éxito
define ( 'IMG_WARNING', 'im/amarillo.png' ); // Imagen a mostrarse en los avisos de warning
define ( 'IMG_ERROR', 'im/rojo.png' ); // Imagen a mostrarse en los avisos de error o fracaso

define ( 'MYSQL_HOST', 'localhost' ); // your db's host
define ( 'MYSQL_PORT', 3306 ); // your db's port
define ( 'MYSQL_USER', 'framework' ); // your db's username
define ( 'MYSQL_PASS', 'holamundo' ); // your db's password
define ( 'MYSQL_NAME', 'framework' ); // your db's database name
define ( 'DBCHAR', 'utf8' ); // The DB's charset

define ( 'DB_SHOW_ERRORS', TRUE ); // Show DB connection error to users?
define ( 'DB_DATASIZE', FALSE ); // NOT recommended for large queries! Haves an significant impact on speed!!
define ( 'DB_LOG_XML', FALSE ); // Log all database activity to XML?
define ( 'DB_URL_XML', '/home/' ); // Location of XML file, recommended place is outside the public_html directory!
define ( 'DB_CACHE_LOCATION', PUBLICROOT.'cache/' ); // Location of cache file(s), with trailing slash
define ( 'DB_CACHE_EXPIRE', '60' ); // DB cache file expiricy, in seconds

define ( 'LOGLEVEL', 15 );
define ( 'LOGLOCATION', '/home/sgp/logs-sistema/' );
define ( 'LOG_AJAX_REQUESTS', FALSE );

/*************************************************\
 *************************************************|
|     NO EDITAR DE AQUI HACIA ABAJO !!            |
 *************************************************|
 *************************************************/
$r ['tit'] = '';
$r ['onload'] = '';
$r ['submenu'] [] = array ('link' => HOME, 'txt' => 'Inicio', 'spec' => TRUE );
$r ['logeado'] = FALSE;
$r ['js'] = '';
$r ['css'] = '';
$r ['script'] = '';
$r ['style'] = '';
$r ['myhome'] = '';
$r ['user_name'] = '';
$r ['user_id'] = 0;
$r ['desarrollo'] = FALSE;
$r ['ayuda'] = array ();
$r ['load_headers'] = TRUE;
$r ['options'] = array ();
$CSSErrors = array ();
