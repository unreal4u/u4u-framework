<?php
/*
$Rev: 440 $
$Date: 2011-01-14 13:53:59 -0300 (Fri, 14 Jan 2011) $
$Author: unreal4u $
*/
/**
 * Muestra de página no encontrada
 * @package General
 * @author Camilo Sperberg
 */

$app->isPublicPage = true;
$app->view->pageTitle = __('404 Not found');
header('x', true, 404);
#$app->misc->logActivity($r['id_user'], '404', $_SERVER['REQUEST_URI']);
echo $app->misc->c_title(__('We are sorry, but what you are looking for isn\'t here'), sprintf(__('Please, %s to choose the right option'), $app->he->c_href(HOME,__('go back to our index'))));
echo $app->he->c_tag('p',__('It is also possible that this page is under development right now'));
