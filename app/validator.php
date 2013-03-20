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
include(ROUT.'basics.php');
include(THIRDPARTY_DIRECTORY.'enabled-thirdparty-extensions.php');
include(CLASSES.'appContainer.class.php');

// Create the $app object
$app = new appContainer();

// Register the most basic classes
$app->initializeMainObject();

// Include PHPGETTEXT
$app->includeThirdparty(TP_PHPGETTEXT);

// @TODO Make this dynamic!
T_setlocale(LC_MESSAGES, 'es_CL');
bindtextdomain('messages', LOCALE_DIR);
if (function_exists('bind_textdomain_codeset')) {
    bind_textdomain_codeset('messages', CHARSET);
}
textdomain('messages');

// Find out what the requested page is
if (empty($_GET['p'])) {
    if (HOMEPAGE !== 'index/index/') {
        $app->misc->redir(HOME . HOMEPAGE);
    }
    $_GET['p'] = HOMEPAGE;
}

// Find out what we must load
$app->validateRoute($_GET['p']);

if (empty($app->module) OR !is_readable(CONTROLLERS . $app->module . '.php')) {
    $app->module = 'index/not-found';
}

// Load the framework's options
$app->loadOptions();

if (empty($app->options['installed']) and $app->module != 'install/index') {
    $app->misc->redir(HOME . 'install/');
}
if (empty($app->options['active_theme'])) {
    $app->options['active_theme'] = 'default';
}

$app->sessionId = session_id();

if (empty($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
} else {
    if (APP_ENVIRONMENT === 'dev') {
        $sesinfo = (time() + SESION_EXPIRE) - $_SESSION['timeout'];
        if ($sesinfo < 0) {
            $sesinfo = abs($sesinfo) + SESION_EXPIRE;
        }
    }
    if (time() > $_SESSION['timeout']) {
        $_SESSION['loggedIn'] = false;
        $app->misc->redir(HOME . 'logout/');
    } else {
        $_SESSION['timeout'] = time() + SESION_EXPIRE;
    }
}

$app->loggedIn = $_SESSION['loggedIn'];

// Creating CSS class and adding default css file to it
$app->css->resetCSS = true;
$app->css->add(USER_SPACE . 'themes/' . $app->options['active_theme'] . '/css/base.css');

if ($app->loggedIn === true) {
    $r['id_user'] = $_SESSION['id_user'];
    #$app->loginUsername = $_SESSION['loginUsername'];
    $result = $app->setMenuAndCheck();
    if ($result === 1) {
        $this->msgStack->add(2, __('You don\'t have access to any option in the menu and you just will have access to the most basic options.<br />If you believe it\'s a mistake, talk with an administrator.'));
    }
    unset($result);
}

// Including the base breadcrump
$app->bc->add(HOME, __('Home'));
$pantalla = '';
$i = 0;
ob_start();
include (CONTROLLERS . $app->module . '.php');
$app->pageContents = ob_get_contents();
ob_end_clean();
// After including the page we are trying to visit, some additional checks
if (!empty($app->isAjaxRequest)) {
    $app->loadHeaders = false;
    $app->he->c_closebody();
    $app->he->c_closehtml();
    if (!isset($_SERVER['HTTP_REFERER']) or strpos($_SERVER['HTTP_REFERER'], HOME) === false) {
        die(__('Access denied'));
    }
} else {
    if ($app->loggedIn and !$app->found and empty($app->isPublicPage)) {
        #$app->misc->redir(HOME . 'no-permission/');
    }
    if (!isset($app->isPublicPage) and $app->loggedIn == false) {
        $app->misc->redir(HOME . 'login/');
    }
    #if (APP_ENVIRONMENT === 'dev' and !in_array(2, $_SESSION['id_grp'])) {
    #    $app->misc->redir(HOME . 'no-permission/');
    #}
}
// Empezamos a limpiar y logear la acción realizada
if (!$app->loggedIn) {
    unset($r['submenu']);
} else {
    if (empty($app->isAjaxRequest)) {
        $app->misc->logActivity($r['id_user'], 'pag', $_SERVER['REQUEST_URI']);
    } else if (LOG_AJAX_REQUESTS === true) {
        $app->misc->logActivity($r['id_user'], 'ajx', $_SERVER['REQUEST_URI']);
    }
}

include (ROUT . 'c_header.php');
echo $app->pageContents;
include (ROUT . 'c_footer.php');

