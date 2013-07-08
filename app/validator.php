<?php
/*
 * The heart of the system, it does a lot of things, among:
 *
 * <ul><li>Load the basic environment</li>
 * <li>Check for permissions</li>
 * <li>Creates the session</li>
 * <li>Creates several objects such as database, messages, CSS, etc</li>
 *
 * Author: Camilo Sperberg - http://unreal4u.com/
 */
include(dirname(__FILE__).'/config.php');
include(THIRDPARTY_DIRECTORY.'enabled-thirdparty-extensions.php');
include(CLASSES.'appContainer.class.php');
include(CLASSES.'controller.class.php');

// Create the $app object
$app = new appContainer();

// Register the most basic classes and include PHP_GETTEXT
$app->initializeMainObject()->includeThirdparty(TP_PHPGETTEXT);

// @TODO Make this dynamic!
T_setlocale(LC_MESSAGES, 'es_CL');
bindtextdomain('messages', LOCALE_DIR);
if (function_exists('bind_textdomain_codeset')) {
    bind_textdomain_codeset('messages', CHARSET);
}
textdomain('messages');

// Find out what we must load
$app->validateRoute();

// Load the framework's options
$app->loadOptions();

// Creating CSS class and adding default css file to it
$app->css->resetCSS = true;
$app->css->add(USER_SPACE . 'themes/' . $app->options['active_theme'] . '/css/base.css');

if ($app->loggedIn === true) {
    $app->idUser = $_SESSION['idUser'];
    if ($app->setMenuAndCheck() === 1) {
        $this->msgStack->add(2, __('You don\'t have access to any option in the menu and you just will have access to the most basic options.<br />If you believe it\'s a mistake, talk with an administrator.'));
    }
}

// Including the base breadcrump
$app->bc->add(HOME, __('Home'));
// Sets up the view and executes the page... @TODO optimize this later on (the view part)
$app->execute('smarty');

// After including the page we are trying to visit, some additional checks
if (!empty($app->isAjaxRequest)) {
    $app->loadHeaders = false;
    $app->he->c_closebody();
    $app->he->c_closehtml();
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], HOME) === false) {
        die(__('Access denied'));
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
