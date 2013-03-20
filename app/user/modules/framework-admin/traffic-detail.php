<?php
/**
 * Consulta Detalle de tráfico por usuarios
 *
 * @package General
 * @author Camilo Sperberg
 */
define('RESULTADOS_POR_PAGINA', 100);
$app->view->pageTitle = __('Traffic detail per users');
$cmd = '';
if (isset($_GET['a'])) {
    $cmd = $_GET['a'];
}
if (isset($cmd) and !empty($_POST['usuario'])) {
    $app->misc->redir($app->myHome . $_POST['usuario'] . '/');
}
$aRes = $app->db->query('SELECT id_user,login FROM sist_users ORDER BY created DESC');
foreach ($aRes as $a) {
    if ($cmd == $a['login'])
        $selected = ' selected="selected"';
    else
        $selected = '';
    $aUsuarios[] = array(
        'id_usuario' => $a['id_user'],
        'nombre_usuario' => $a['login'],
        'selected' => $selected
    );
}
$pantalla = '<form action="' . $app->myHome . '" method="post" id="usuarios" class="centrar"><select id="usuario" name="usuario">';
foreach ($aUsuarios as $a) {
    if ($cmd == '')
        $app->misc->redir($app->myHome . $a['nombre_usuario'] . '/');
    $pantalla .= '<option value="' . $a['nombre_usuario'] . '"' . $a['selected'] . '>' . $a['nombre_usuario'] . '</option>';
    if ($a['selected'] != '')
        $id = $a['id_usuario'];
}
$pantalla .= '</select></form><script type="text/javascript">$("#usuario").change(function(){$("#usuarios").submit();});</script>';
$aNumeroPagina = $app->db->query('SELECT COUNT(id_user) AS num FROM sist_activity WHERE id_usuario = ?', $id);
include (CLASSES . 'paginator.class.php');
$pager = new paginator(RESULTADOS_POR_PAGINA);
$paginador = $pager->c_html($aNumeroPagina[0]['num'], $app->myHome . '&cmd=' . $cmd);
$aLogeos = $app->db->query('SELECT COUNT(k) AS logeos FROM sist_activity WHERE k = ? AND id_user = ?', 'loi', $id);
$pantalla .= '<p class="centrar">Cantidad de logeos al sistema: <strong>';
if ($app->db->num_rows > 0)
    $pantalla .= $aLogeos[0]['logeos'];
else
    $pantalla .= '0';
$pantalla .= '</strong></p><hr />' . $paginador['html'];
$aRes = $app->db->query('SELECT k,v AS pagina,DATE_FORMAT(at,\'%d-%m-%Y %H:%i:%S\') AS fecha,UNIX_TIMESTAMP(at) AS unix FROM sist_activity WHERE id_user = ? ORDER BY at DESC LIMIT ?,?', $id, $paginador['offset'], $paginador['limit']);
if ($app->db->num_rows > 0) {
    $colores = array(
        'pag' => array(
            'trclass' => '',
            'tipo' => 'Visita'
        ),
        'loi' => array(
            'trclass' => ' style="background:#CCF"',
            'tipo' => '<strong>Login</strong>'
        ),
        'lou' => array(
            'trclass' => ' style="background:yellow"',
            'tipo' => 'Logout'
        ),
        'tim' => array(
            'trclass' => ' style="background:yellow"',
            'tipo' => 'Sesion Timeout'
        ),
        'pwd' => array(
            'trclass' => ' style="background:#CCC"',
            'tipo' => '<strong>Cambio de Contrase&ntilde;a</strong>'
        ),
        'pro' => array(
            'trclass' => ' style="background:#7F1F1F;color:#FFF"',
            'tipo' => 'Acceso directo no permitido!'
        ),
        'red' => array(
            'trclass' => ' style="background:#AAA"',
            'tipo' => 'Redirecci&oacute;n'
        ),
        'cia' => array(
            'trclass' => ' style="background:red;color:#FFF"',
            'tipo' => 'Logeo cuenta inactiva!'
        ),
        'pic' => array(
            'trclass' => ' style="background:#666;color:#FFF"',
            'tipo' => 'Intento de inicio sesi&oacute;n'
        )
    );
    $pantalla .= '<p id="muestra_leyenda" class="puntero">Mostrar Leyenda</p><table class="escondido" id="leyenda" style="margin:0;position:fixed">';
    foreach ($colores as $a) {
        $pantalla .= '<tr><td' . $a['trclass'] . '>&nbsp;&nbsp;</td><td>' . strip_tags($a['tipo']) . '</td></tr>';
    }
    $pantalla .= '</table>';
    $pantalla .= $app->he->c_javascript('$("#muestra_leyenda").click(function(){$("#leyenda").toggle(500);});');
    $pantalla .= '<table><thead><tr><th>Fecha</th><th>Diferencia<br />D&iacute;as</th><th>P&aacute;gina</th><th>Tipo<br />Actividad</th></tr></thead><tbody>';
    foreach ($aRes as $a) {
        $trclass = ' style="background:#666;color:#FFF"';
        $tipo = '<strong><em>Desconocido (' . $a['k'] . ')</em></strong>';
        if (!empty($colores[$a['k']])) {
            $trclass = $colores[$a['k']]['trclass'];
            $tipo = $colores[$a['k']]['tipo'];
        }
        $diferencia = floor((time() - $a['unix']) / 60 / 24 / 60);
        $pantalla .= '<tr' . $trclass . '><td><div class="centrar" style="width:60px">' . str_replace(' ', '<br />', $a['fecha']) . '</div></td><td class="centrar"><div style="width:75px">' . $diferencia . ' d&iacute;a(s) atr&aacute;s</div><td><div class="auto-overflow" style="width:250px">' . $a['pagina'] . '</div></td><td><div class="auto-overflow" style="width:80px">' . $tipo . '</div></td></tr>';
    }
    $pantalla .= '</tbody></table>' . $paginador['html'];
} else {
    $pantalla .= '<h3 class="centrar">Para el usuario seleccionado, no se encontraron registros</h3>';
}
echo $pantalla;
