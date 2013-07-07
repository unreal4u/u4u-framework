<?php
/*
$Rev: 441 $
$Date: 2011-01-14 15:44:24 -0300 (Fri, 14 Jan 2011) $
$Author: unreal4u $
*/
/**
 * El manejador de errores, captura el error y es capaz de logearlo
 * @package Internals
 * @author unreal4u
 * @version 1.1
 */

error_reporting(-1);
ini_set('display_errors',1);

/**
 * Función que hace toda la acción en caso de existir algún problema
 * @param int $errno El número de error
 * @param string $errstr La cadena identificatoria del problema
 * @param string $errfile El archivo en que ocurre el problema
 * @param int $errline La línea en que ocurre el problema
 * @param array $errctx Un arreglo que contiene un dump del problema
 */
function dramas($errno = '0', $errstr = 'Error General', $errfile = 'N/A', $errline = 'N/A', $errctx = '') {
  global $pantalla;
  global $todo_limpio;
  global $CSSErrors;
  global $dbErrors;
  global $proc;
  if (!$todo_limpio) {
    $proc = TRUE;
    if (empty($errctx)) $errctx = array();
    //if (!defined('INCL')) define('INCL','inc/');
    if (!defined('HOME')) include(dirname(__FILE__).'/config.php');
    if (!class_exists('messageStack')) include(ROUT.'messages.class.php');
    if (!class_exists('DB_mysql')) include(ROUT.'mysql.class.php');
    if (!class_exists('messageStack')) include(ROUT.'messages.class.php');
    if (!class_exists('CSStacker')) include(ROUT.'css.class.php');
    if (!class_exists('HTMLUtils')) include(ROUT.'html_utils.class.php');
    if (!function_exists('get_current_revision')) include(ROUT.'basics.php');
    $app->db = new DB_mysql();
    $app->he = new HTMLUtils();
    if (ob_get_contents() != '') ob_end_clean();
    $r['logeado'] = FALSE;
    $sesinfo = 0;
    $app->view->pageTitle = 'Error';
    $r['empresa'] = 'Calalsa Industrial';

    $msgStack = new messageStack();
    #$app->css = new CSStacker();
    #$app->css->resetCSS = TRUE;
    #$app->css->add(USER_SPACE.'css/base.css');

    $q = $_SERVER['REQUEST_TIME'] + microtime();

    ob_start();
    include(RUTA.INCL.'header.php');
    echo $app->he->c_tag('h1',__('We\'re sorry, but an error happened'));
    echo $app->he->c_tag('h2',__('There\'s been an internal error that prevents the page from loading correctly.'));
    echo $app->he->c_tag('h2',sprintf(__('This error has been saved so that an administrator can check it. However, you can write to <a href="mailto:%s">%s</a> to report it.'),$_SERVER['SERVER_ADMIN'],$_SERVER['SERVER_ADMIN']));
    echo $app->he->c_tag('p',$app->he->c_href(HOME,__('Back to home')),'center');
    $contenido = ob_get_contents();
    ob_end_clean();

    echo $contenido;

    $contenido_error  = '<p style="font-size:110%;margin:25px 0 20px 25px"><em>N&deg; del error:</em><br /><strong>'.$errno.'</strong><br />';
    $contenido_error .= '<em>Detalle del error:</em><br /><strong>'.$errstr.'</strong><br /><em>Archivo:</em><br /><strong>'.$errfile.'</strong><br /><em>L&iacute;nea:</em><br /><strong>'.$errline.'</strong><br /><em>Revisi&oacute;n software:</em><br /><strong>'.get_current_revision().'</strong>';
    $contenido_error .= '<br /><em>Debug:</em><br />';
    if (!empty($errctx)) $contenido_error .= $app->he->pre($errctx,FALSE);
    $contenido_error .= $app->he->pre($CSSErrors,FALSE);
    $contenido_error .= $app->he->pre($dbErrors,FALSE);
    $contenido_error .= '</p>';
    if (APP_ENVIRONMENT != 'production') echo $contenido_error;
    if ($errline == 'N/A') $errline = 0;
    $user_id = 0;
    if (is_array($errctx)) if (!empty($errctx['clean']['user_id'])) $user_id = $errctx['clean']['user_id'];
    $app->db->query('INSERT INTO sist_errores(errno,errstr,errfile,errline,errctx,id_usuario,hora) VALUES (?,?,?,?,?,?,NOW())',$errno,$errstr,$errfile,$errline,$contenido_error,$user_id);
    include(ROUT.'footer.php');
    die();
  }
}
