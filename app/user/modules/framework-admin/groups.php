<?php
/*
$Rev: 440 $
$Date: 2011-01-14 13:53:59 -0300 (Fri, 14 Jan 2011) $
$Author: unreal4u $
*/
/**
 * Administrador de Grupos del sistema
 * @package General
 * @author Camilo Sperberg
 */


$app->view->pageTitle = __('Roles Modification');
?><h1><?php
echo $app->view->pageTitle;
?></h1><h2>Esta p&aacute;gina sirve para modificar los grupos existentes en el sistema</h2><?php
include(CLASSES.'ediciones.class.php');
$ediciones = new ediciones();
$ediciones->MySQL = TRUE;
$ediciones->tabla = 'sist_grp';
$ediciones->singular = 'grupo';
$ediciones->campo_id = 'id_grp';
$ediciones->campo_descripcion = 'description';
$ediciones->muestra_id = TRUE;

$r['onload'] = $ediciones->onload();
$ediciones->execute($_POST);
$app->javascriptCode[] = $ediciones->constructJS();
$ediciones->printHTML();
unset($ediciones);
