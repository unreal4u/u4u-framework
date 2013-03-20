<?php
/**
 * Leftmenu, el script que crea el menú lateral
 *
 * Es la encargada de crear el menú lateral izquierdo para cada uno de los usuarios,
 * revisa en la base de datos cuáles son las opciones a las que el usuario tiene
 * acceso y carga estos.
 *
 * @package Internals
 * @author unreal4u
 * @version 1.1
 */

$pantalla = '<div id="leftmenu">';
if (!empty($app->menu[0])) {
  foreach($app->menu AS $a) {
    $current = '';
    if ($a['link'] == $app->myHome) $current = 'current';
    $pantalla .= $app->he->c_href($a['link'],$a['description'],'',$current);
  }
}
//else $pantalla .= $app->he->c_href(HOME,'Inicio').$app->he->c_href(HOME.'logout','Salir'); //'<a href="'.HOME.'">Inicio</a><a href="'.HOME.'logout">Salir</a>';

echo $pantalla.'</div>';
