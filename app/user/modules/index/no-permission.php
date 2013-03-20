<?php
/**
 * Muestra acceso prohibido
 * @package General
 * @author Camilo Sperberg
 */

$app->isPublicPage = true;
$app->view->pageTitle = __('403 Forbidden access');
header('x', true, 403);
echo $app->misc->c_title(__('We are sorry, but you have no permission to see this page'), sprintf(__('Please, %s to choose the right option'), $app->he->c_href(HOME,__('go back to our index'))));
echo $app->he->c_tag('p',__('It is also possible that this page is under development right now'));
