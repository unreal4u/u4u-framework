<?php
/**
 * The user defined header
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
    print($app->he->c_html());
    if (isset($app->css)) {
        $cssLink = $app->css->printme('filename');
        if (!empty($cssLink)) {
            print($app->he->c_link(HOME.$cssLink));
        }
    } else {
        print($app->he->c_link(HOME . 'js/min.css'));
    }
    print($app->he->c_link(HOME . IMAG . 'favicon.ico', 'shortcut'));
    print($app->he->c_script(array(
        HOME . JSCR . $app->options['jquery_main'], HOME . JSCR . 'common.js'
    )));
    if (!empty($app->javascriptFiles[0])) {
        foreach ($app->javascriptFiles as $a) {
            print($app->he->c_script($a));
        }
    }
    #if (!empty($r['ayuda'][0])) {
    #    $print .= $app->he->c_script(HOME . INCL . 'js/jquery.tooltip.js');
        //$print .= $app->he->c_javascript('$(document).ready(function(){$(\'a:not([href^="'.HOME.'"])\').attr("target","_BLANK");});');
    #}
    if (!empty($app->javascriptCode[0])) {
        foreach ($app->javascriptCode as $a) {
            print($app->he->c_javascript($a));
        }
    }
    #if (isset($r['meta']) and !empty($r['meta'][0])) {
    #    foreach ($r['meta'] as $a) {
    #        $print .= $app->he->c_meta($a[0], $a[1]);
    #    }
    #}
    if (empty($app->options['sitename'])) {
        $app->options['sitename'] = _('u4u Framework');
    }

    if (!empty($app->pageTitle)) {
        $app->pageTitle = $app->pageTitle . ' &laquo; ' . $app->options['sitename'];
    } else {
        #$app->view->pageTitle = 'Inicio &laquo; ' . $app->options['sitename'];
    }
    print($app->he->c_title($app->pageTitle));
    #if (!empty($r['styles'])) {
    #    $print .= $app->he->c_style($r['styles']);
    #}
    #if (empty($r['onload'])) {
    #    $r['onload'] = '';
    #}
    #$print .= $app->he->c_body($r['onload']);
    unset($a);
    include (USER_SPACE . 'themes/' . $app->options['active_theme'] . '/header.php');
}