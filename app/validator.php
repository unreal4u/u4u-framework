<?php
/**
 * The heart of the system, it does a lot of things, among:
 *
 * <ul><li>Load the basic environment</li>
 * <li>Check for permissions</li>
 * <li>Creates the session</li>
 * <li>Creates several objects such as database, messages, CSS, etc</li>
 * <li>Sets the base directory</li></ul>
 *
 * Author: Camilo Sperberg - http://unreal4u.com/
 */

chdir(dirname(__FILE__));
include 'classes/framework/appContainer.class.php';

// Create the $app object
$app = new appContainer();

// Loads the configuration
// @TODO Make this dynamic!
$app->loadConfiguration('user/configurations/default');

include(THIRDPARTY_DIRECTORY.'enabled-thirdparty-extensions.php');
include(CLASSES.'controller.class.php');

// Register the most basic classes
$app->initializeMainObject();

// Locale settings
// @TODO Make this dynamic!
$app->setLocale('es_CL');

// Find out what we must load and load the framework's options
$app->validateRoute()->loadOptions();

// Creating CSS class and adding default css file to it
$app->css->resetCSS = true;
$app->css->add(USER_SPACE . 'themes/' . $app->options['active_theme'] . '/css/base.css');

if ($app->loggedIn === true) {
    $app->idUser = $_SESSION['idUser'];
    if ($app->setMenuAndCheck() === 1) {
        $this->msgStack->add(2, _('You don\'t have access to any option in the menu and you just will have access to the most basic options.<br />If you believe it\'s a mistake, talk with an administrator.'));
    }
}

$app->bc = new breadcrump();
// Including the base breadcrump
$app->bc->add(HOME, _('Home'));
// Sets up the view and executes the page... @TODO optimize this later on (the view part)
$app->execute('smarty');

// After including the page we are trying to visit, some additional checks
if (!empty($app->isAjaxRequest)) {
    $app->loadHeaders = false;
    $app->he->c_closebody();
    $app->he->c_closehtml();
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], HOME) === false) {
        die(_('Access denied'));
    }
} else {
    if ($app->loggedIn && !$app->found && empty($app->isPublicPage)) {
        #$app->misc->redir(HOME . 'no-permission/');
    }

    if (!isset($app->isPublicPage) && $app->loggedIn == false) {
        $app->misc->redir(HOME . 'login/');
    }
    #if (APP_ENVIRONMENT != 'production' and !in_array(2, $_SESSION['id_grp'])) {
    #    $app->misc->redir(HOME . 'no-permission/');
    #}
}
// Empezamos a limpiar y logear la acción realizada
if (!$app->loggedIn) {
    #unset($r['submenu']);
} else {
    #if (empty($app->isAjaxRequest)) {
    #    $app->misc->logActivity($r['idUser'], 'pag', $_SERVER['REQUEST_URI']);
    #} else if (LOG_AJAX_REQUESTS === true) {
    #    $app->misc->logActivity($r['idUser'], 'ajx', $_SERVER['REQUEST_URI']);
    #}
}
