<?php
/*
$Rev: 442 $
$Date: 2011-01-14 16:28:41 -0300 (Fri, 14 Jan 2011) $
$Author: unreal4u $
*/
/**
 * Este script crea el submenú de la página.
 *
 * Siempre habrán mínimo 4 opciones:
 * - Inicio -> para llevar al inicio del sitio.
 * - Cambiar Contraseña -> que lleva al script donde se puede cambiar la pass
 * - Agenda -> Es un índice con todos los anexos de la empresa.
 * - Salir -> Para salir del sistema.
 *
 * @package Internals
 * @author unreal4u
 * @version 1.1
 */
if ($app->loggedIn) {
    $r['submenu'][] = array(
        'link' => 'passwd/', 'txt' => _('Change Password'), 'spec' => TRUE
    );
    $r['submenu'][] = array(
        'link' => 'logout/', 'txt' => _('Logout'), 'spec' => TRUE
    );
}
if (!empty($r['submenu'][0])) {
    $pantalla = '';
    foreach ($r['submenu'] as $a) {
        $pagina = trim($_SERVER['REQUEST_URI'], '/');
        $tmpPag = substr($pagina, strrpos($pagina, '/'));
        if ($tmpPag !== FALSE)
            $pagina = $tmpPag;
        if ($a['link'] == $pagina)
            $special = 'sub_current';
        elseif (!empty($a['spec']))
            $special = 'sub_special';
        else
            $special = '';
        $pantalla .= $app->he->c_href($a['link'], $a['txt'], '', $special);
    }
    unset($special, $pagina, $tmpPag);
    echo '<div id="submenu">' . $pantalla . '</div>';
}
unset($r['submenu']);