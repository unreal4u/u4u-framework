<?php
/**
 * Module description
 *
 * @package General
 * @version $Rev$
 * @copyright $Date$
 * @author $Author$
 * @license BSD License. Feel free to use and modify
 */

$app->isPublicPage = true;
$app->view->pageTitle = __('Main index');

echo '<div>'.$app->he->c_href('login/',__('Go to login')).'</div>';
