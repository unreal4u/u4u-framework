<?php

$app->view->pageTitle = _('System errors');
echo $app->misc->c_title($app->view->pageTitle, _('You can check here for all errors made by the system.'));
$pager['html'] = '';
if (isset($_GET['cmd']) and is_numeric($_GET['cmd'])) {
    $aRes = $myLink->query('SELECT a.id_error,a.errno,a.errstr,a.errfile,a.errline,a.errctx,a.id_usuario,b.nombre_usuario,DATE_FORMAT(a.hora,\'%d-%m-%Y %H:%i:%S\') AS fecha,a.visto,a.reparado,a.nota FROM sist_errores AS a LEFT JOIN usuarios AS b ON a.id_usuario = b.id_usuario WHERE a.id_error = ? LIMIT ?', $_GET['cmd'], 1);
    if ($myLink->num_rows > 0) {
        $a = $aRes[0];
        if (!empty($_POST['nota']) or !empty($_POST['revision'])) {
            $myLink->query('UPDATE sist_errores SET nota = ?, reparado = ?,hora = hora WHERE id_error = ?', $_POST['nota'], $_POST['revision'], $_GET['cmd']);
            $a['nota'] = $_POST['nota'];
            $a['reparado'] = $_POST['revision'];
            $msgStack->add(3, 'Informe correctamente actualizado. ' . $app->he->c_href($app->myHome, 'Volver a Listado de Errores'));
        }
        $r['onload'] = '$(\'#revision\').select().focus();';
        $app->view->pageTitle .= ' - bug #' . $a['id_error'];
        if (!empty($a['id_usuario'])) {
            $a['nombre_usuario'] = $app->he->c_href('admin-users&cmd=detalles&a=' . $a['id_usuario'], $a['nombre_usuario']);
        }
        $pantalla = '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="ppal"><table>';
        $pantalla .= '<tr><td>Tipo de Error:</td><td>' . $a['errno'] . '<br /><strong>' . c_error($a['errno']) . '</strong></td><td>Archivo / L&iacute;nea</td><td>' . $a['errfile'] . '<br />' . $a['errline'] . '</td></tr>';
        $pantalla .= '<tr><td>Resumen:</td><td colspan="3" style="padding:7px 2px"><strong>' . $a['errstr'] . '</strong></td></tr>';
        $pantalla .= '<tr><td>Hora del suceso:</td><td class="centrar"><strong>' . str_replace(' ', '<br />', $a['fecha']) . '</strong></td><td>Por: </td><td>' . $a['nombre_usuario'] . '</td></tr>';
        $pantalla .= '<tr><td colspan="4"><div style="width:700px;height:350px;overflow:scroll">' . $a['errctx'] . '</div></td></tr>';
        $pantalla .= '<tr><td>Solucionado en revisi&oacute;n:</td><td class="centrar"><input type="text" value="' . $a['reparado'] . '" name="revision" id="revision" /></td><td class="centrar">Nota Administrativa:</td><td class="centrar"><input type="text" value="' . str_replace('"', '&quot;', $a['nota']) . '" name="nota" maxlength="255" style="width:300px" /></td></tr>';
        $pantalla .= '<tr><td class="centrar" colspan="4"><input type="submit" value="Guardar cambios" /></td></tr>';
        $pantalla .= '</table>';
        if (empty($a['visto']))
            $myLink->query('UPDATE sist_errores SET visto = ?,hora = hora WHERE id_error = ?', 1, $_GET['cmd']);
    } else {
        $app->misc->redir($app->myHome);
    }
} else {
    if (isset($_POST['la_seleccion']) and isset($_POST['rev_info']) and is_array($_POST['chk_seleccion'])) {
        reset($_POST['chk_seleccion']);
        $cadena = '';
        $i = 0;
        foreach ($_POST['chk_seleccion'] as $a) {
            if ($i > 0)
                $cadena .= ' OR ';
            $cadena .= 'id_error = ' . $a;
            $i++;
        }
        $aRes = FALSE;
        switch ($_POST['la_seleccion']) {
            case 0:
                $aRes = $myLink->query('UPDATE sist_errores SET hora=hora,visto=?,duplicado=? WHERE ' . $cadena, 1, $_POST['rev_info']);
            break;
            case 1:
                $aRes = $myLink->query('DELETE FROM sist_errores WHERE ' . $cadena);
            break;
            case 2:
                $aRes = $myLink->query('UPDATE sist_errores SET hora=hora,visto=? WHERE ' . $cadena, 1);
            break;
            default: //$msgStack->add(2,'Lo siento, la opci&oacute;n que seleccion&oacute; es inv&aacute;lida. Por favor intente nuevamente');
            break;
        }
        if ($aRes !== FALSE) {
            $msgStack->add(3, 'Los registros seleccionados han sido modificados o borrados exitosamente');
        } else {
            $msgStack->add(2, 'Ha ocurrido un error interno o seleccion&oacute; una opci&oacute;n incorrecta. Por favor, intente nuevamente');
        }
        $app->misc->redir($_SERVER['REQUEST_URI']);
    }
    $ver = 0;
    if (isset($_POST['ver'])) {
        $ver = $_POST['ver'];
    }
    $pantalla = '<form action="' . $_SERVER['REQUEST_URI'] . '" class="centrar" method="post" id="ppal"><select name="ver" onchange="javascript:$(\'#ppal\').submit();">';
    $opciones = array(
        0 => 'Todos',
        1 => 'No vistos',
        2 => 'No reparados',
        3 => 'Reparados'
    );
    foreach ($opciones as $k => $v) {
        if ($ver == $k) {
            $selected = ' selected="selected"';
        } else {
            $selected = '';
        }
        $pantalla .= '<option value="' . $k . '"' . $selected . '>' . $v . '</option>';
    }
    $pantalla .= '</select></form>';
    if ($ver == 0) {
        $filtrar = '';
    } elseif ($ver == 1) {
        $filtrar = ' AND a.visto = 0';
    } elseif ($ver == 2) {
        $filtrar = ' AND a.reparado = 0';
    } elseif ($ver == 3) {
        $filtrar = ' AND a.reparado != 0';
    }
    include (CLASSES . 'paginator.class.php');
    $paginator = new paginator();
    $cuantos = $app->db->query('SELECT count(id_error) AS num FROM sist_errores WHERE duplicado = 0');
    $pager = $paginator->c_html($cuantos[0]['num'], HOME . 'errores');
    echo $pager['html'];
    //$app->he->pre($pager);
    $aRes = $app->db->query('SELECT a.id_error,a.errno,a.errstr,a.errfile,a.errline,a.id_usuario,b.nombre_usuario,DATE_FORMAT(a.hora,\'%d-%m-%Y %H:%i:%S\') AS fecha,a.visto,a.reparado,a.nota FROM sist_errores AS a LEFT JOIN usuarios AS b ON a.id_usuario = b.id_usuario WHERE duplicado = 0' . $filtrar . ' ORDER BY a.id_error DESC LIMIT ?,?', $pager['offset'], $pager['limit']);
    if ($app->db->num_rows > 0) {
        $app->javascriptCode[] = 'function chk(a){$("input[name=chk_seleccion\\[\\]]").attr(\'checked\',a);}';
        $pantalla .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="seleccion"><table><thead><tr><th>ID</th><th>Sel.</th><th>Hora</th><th>N&deg; &amp; Tipo</th><th>Resumen</th><th>Archivo/L&iacute;nea</th><th>Usuario</th><th>Reparado<br />en Rev.</th><th>Acciones</th></tr></thead><tbody>';
        foreach ($aRes as $a) {
            $trclass = '';
            if ($a['visto'] == 0)
                $trclass = ' style="background:#CCF"';
            if (empty($a['reparado']))
                $a['reparado'] = '-';
            $pantalla .= '<tr' . $trclass . '><td class="centrar"># <strong>' . $a['id_error'] . '</strong></td><td class="centrar"><input type="checkbox" name="chk_seleccion[]" value="' . $a['id_error'] . '" /></td><td class="centrar">' . str_replace(' ', '<br />', $a['fecha']) . '</td><td class="centrar"><strong>' . $a['errno'] . '</strong><br />' . c_error($a['errno']) . '</td><td><div class="auto-overflow" style="width:150px">' . $a['errstr'] . '</div></td><td>' . $a['errfile'] . '<br /><span class="centrar" style="font-weight:bold">' . $a['errline'] . '</span></td><td>' . $a['nombre_usuario'] . '</td><td class="centrar"><span title="' . $a['nota'] . '">' . $a['reparado'] . '</span></td><td class="centrar">' . $app->he->c_href($app->myHome . '&cmd=' . $a['id_error'], $app->he->c_img('im/edit.png')) . '</td></tr>';
        }
        $pantalla .= '</tbody></table>';
        $pantalla .= '<table class="sin-borde"><tr><td><span class="like-a-link" id="selecciona_todos" onclick="javascript:chk(true);">Seleccionar Todos</span><br /><span class="like-a-link" id="selecciona_ninguno" onclick="javascript:chk(false);">Seleccionar Ninguno</span></td><td>Con los elementos seleccionados, marcar como: <select name="la_seleccion" id="la_seleccion">';
        $pantalla .= '<option value="0">Duplicado (Llenar dato)</option><option value="1">Borrar</option><option value="2">Marcar como le&iacute;do</option>';
        $pantalla .= '</select><div id="extra">ID #: <input type="text" class="chico" value="0" id="rev_info" name="rev_info" /></div><input type="submit" value="Aceptar" /></td></tr></table></form>';
        $pantalla .= $app->he->c_javascript('$("#la_seleccion").change(function(){var v=$(this).val();if(v==0){$("#extra").show();$("#rev_info").select().focus();}else{$("#extra").hide();}});');
    } else
        $pantalla = $app->he->c_tag('h4', 'Bravo! No existen errores ingresados a la base de datos!');
}
echo $pantalla . $pager['html'];
