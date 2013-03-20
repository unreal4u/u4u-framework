<?php
/**
 * El header, con todas las opciones de construcción
 *
 * @package Internals
 * @author unreal4u
 * @version 1.2
 */
if (!empty($app->loadHeaders)) {
    if (!headers_sent()) {
        header('Server: Apache');
        header('X-Powered-By: PHP');
        header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: post-check=0, pre-check=0');
        header('Cache-Control: no-store, no-cache, must-revalidate', FALSE);
        header('Pragma: no-cache');
        header('Content-type: text/html; charset=' . CHARSET);
    }
    $r['print'] = $app->he->c_html();
    if (isset($app->css)) {
        $r['print'] .= $app->he->c_link(HOME.$app->css->printme('filename'));
    } else {
        $r['print'] .= $app->he->c_link(HOME . 'js/min.css');
    }
    $r['print'] .= $app->he->c_link(HOME . IMAG . 'favicon.ico', 'shortcut');
    $r['print'] .= $app->he->c_script(array(
        HOME . JSCR . $app->options['jquery_main'], HOME . JSCR . 'common.js'
    ));
    if (!empty($app->javascriptFiles[0])) {
        foreach ($app->javascriptFiles as $a) {
            $r['print'] .= $app->he->c_script($a);
        }
    }
    #if (!empty($r['ayuda'][0])) {
    #    $r['print'] .= $app->he->c_script(HOME . INCL . 'js/jquery.tooltip.js');
        //$r['print'] .= $app->he->c_javascript('$(document).ready(function(){$(\'a:not([href^="'.HOME.'"])\').attr("target","_BLANK");});');
    #}
    if (!empty($app->javascriptCode[0])) {
        foreach ($app->javascriptCode as $a) {
            $r['print'] .= $app->he->c_javascript($a);
        }
    }
    if (isset($r['meta']) and !empty($r['meta'][0])) {
        foreach ($r['meta'] as $a) {
            $r['print'] .= $app->he->c_meta($a[0], $a[1]);
        }
    }
    if (empty($app->options['sitename'])) {
        $app->options['sitename'] = 'u4u Framework';
    }
    if (isset($app->view->pageTitle) and !empty($app->view->pageTitle)) {
        $app->view->pageTitle = $app->view->pageTitle . ' &laquo; ' . $app->options['sitename'];
    } else {
        $app->view->pageTitle = 'Inicio &laquo; ' . $app->options['sitename'];
    }
    $r['print'] .= $app->he->c_title($app->view->pageTitle);
    if (!empty($r['styles'])) {
        $r['print'] .= $app->he->c_style($r['styles']);
    }
    if (empty($r['onload'])) {
        $r['onload'] = '';
    }
    $r['print'] .= $app->he->c_body($r['onload']);
    unset($r['meta'], $r['onload'], $a);
    echo $r['print'];
    $r['print'] = '';
    include (USER_SPACE . 'themes/' . $app->options['active_theme'] . '/header.php');
}