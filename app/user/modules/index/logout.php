<?php
/**
 * Página que destruye la sesión y redirige al home.
 *
 * @package General
 * @author Camilo Sperberg
 */
$app->misc->logActivity($r['id_user'], 'lou', 'logout');
session_regenerate_id(true);
$_SESSION = array();
$_SESSION['loggedIn'] = false;
$app->msgStack->add(3, __('You have been successfully logged out. It is now save to leave the computer'));
$app->misc->redir(HOME.'login/');
