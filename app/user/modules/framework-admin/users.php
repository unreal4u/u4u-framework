<?php
/**
 * Administrador de Usuarios
 *
 * @package General
 * @author Camilo Sperberg
 */
echo $app->misc->c_title('Administrador de Usuarios', 'Esta p&aacute;gina le permite administrar los usuarios del sistema y su pertenencia a grupos');
$aGrupos = $app->db->query('SELECT id_grp,description FROM sist_grp ORDER BY id_grp DESC');
if (!empty($_POST['accion'])) {
    $res = FALSE;
    $aID = $app->db->query('SELECT id_user FROM sist_users WHERE login = ? LIMIT 1', $_POST['num_mod']);
    $id_user = $aID[0]['id_user'];
    switch ($_POST['accion']) {
        case 'r':
            $passwd = '345345';
            $hash = md5(uniqid(time() + microtime(), TRUE));
            $tmpPasswd = md5(substr($passwd, 0, round(strlen($passwd) / 2)) . $hash . substr($passwd, round(strlen($passwd) / 2)));
            $res = $app->db->query('UPDATE sist_users SET created = created,passwd = ?,salt_hash = ? WHERE id_user = ?', $tmpPasswd, $hash, $_POST['num_mod']);
        break;
        case 'a':
            $res = $app->db->query('SELECT id_user FROM sist_users WHERE login = ?', $_POST['nombre_usuario']);
            if ($app->db->num_rows > 0) {
                $app->misc->redir($app->myHome . '&cmd=detalles&a=' . $res[0]['id_usuario'] . '&err=70');
            }
            $passwd = '345345';
            $hash = md5(uniqid(time() + microtime(), TRUE));
            $tmpPasswd = md5(substr($passwd, 0, round(strlen($passwd) / 2)) . $hash . substr($passwd, round(strlen($passwd) / 2)));
            $res = $app->db->insert_id('INSERT INTO sist_users (login,passwd,id_empresa,first_name,last_name,created,active,salt_hash) VALUES (?,?,1,?,?,NOW(),?,?)', $_POST['nombre_usuario'], $tmpPasswd, $_POST['nombre'], $_POST['apellido'], $_POST['activo'], $hash);
            if ($res !== FALSE and !empty($_POST['grupo']))
                $res = $app->db->query('INSERT INTO sist_multigroup (id_user,id_grp) VALUES (?,?)', $res, $_POST['grupo']);
        break;
        case 'm':
            $res = $app->db->query('UPDATE sist_users SET created = created,login=?,first_name=?,last_name=?,active=? WHERE id_user=?', $_POST['nombre_usuario'], $_POST['nombre'], $_POST['apellido'], $_POST['activo'], $id_user);
            if (isset($_POST['grupo']) and $_POST['grupo'] == '' or isset($_POST['grupos'])) {
                $app->db->query('DELETE FROM sist_multigroup WHERE id_user = ?', $id_user);
                //pre($_POST['grupos']);
                if (isset($_POST['grupos']))
                    foreach ($_POST['grupos'] as $a)
                        if (!empty($a))
                            $app->db->query('INSERT INTO sist_multigroup (id_user,id_grp) VALUES (?,?)', $id_user, $a);
            }
        break;
    }
    if ($res !== FALSE) {
        $app->msgStack->add(3, 'La operaci&oacute;n seleccionada ha sido ejecutada sin problemas');
    } else {
        $app->msgStack->add(1, 'Ocurri&oacute; un error que no deber&iacute;a haber ocurrido. Por favor avise a un administrador');
    }
    $app->misc->redir(HOME.$app->myHome);
}
$pantalla = '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="ppal">';
if (empty($_GET['a']) or $_GET['a'] != 'detalles') {
    $aNum = $app->db->query('SELECT COUNT(id_user) AS num FROM sist_users');
    include (CLASSES . 'paginator.class.php');
    $pager = new paginator(25);
    $paginador = $pager->c_html($aNum[0]['num'], $app->myHome);
    $pantalla .= $paginador['html'];
    $r['onload'] = '$(\'#usu_a\').focus();';
    $app->javascriptCode[] = 'function ex(a,b){var s=true;var c=0;if(a==\'r\'){s=confirm("Confirma que desea resetear la password del usuario \""+$("#usu_"+b).val()+"\" al predeterminado?");};$("#accion").val(a);$("#num_mod").val(b);$("#nombre_usuario").val($("#usu_"+b).val());$("#nombre").val($("#nom_"+b).val());$("#apellido").val($("#ape_"+b).val());$("#grupo").val($("#gru_"+b).val());if($("#act_"+b).is(":checked")){c=1;};$("#activo").val(c);$("#ppal").submit();};';
    $pantalla .= '<table><thead><tr><th>ID<input type="hidden" name="num_mod" id="num_mod" value="" /></th><th>login<input type="hidden" name="nombre_usuario" id="nombre_usuario" value="" /></th><th>Nombre<input type="hidden" name="nombre" id="nombre" value="" /><input type="hidden" name="apellido" id="apellido" value="" /></th><th>Creado</th><th>Grupo Primario<input type="hidden" name="grupo" id="grupo" value="" /></th><th>Activo<input type="hidden" name="activo" id="activo" value="1" /></th><th>Acciones<input type="hidden" name="accion" id="accion" value="" /></th></tr></thead><tbody>';
    $pantalla .= '<tr><td>&nbsp;</td><td><input type="text" value="" id="usu_a" /></td><td>Nombre:<br /><input type="text" id="nom_a" value="" /></td><td>Apellido:<br /><input type="text" id="ape_a" value="" /></td><td><select id="gru_a"><option value="0" selected="selected">Ninguno</option>';
    foreach ($aGrupos as $a)
        $pantalla .= '<option value="' . $a['id_grp'] . '">' . $a['description'] . '</option>';
    $pantalla .= '</select></td><td class="centrar"><input type="checkbox" value="1" checked="checked" id="act_a" /><td><input type="button" value="Ingresar" onclick="javascript:ex(\'a\',\'a\');" /></td></tr>';
    $aRes = $app->db->query('SELECT a.id_user,a.login,a.first_name,a.last_name,DATE_FORMAT(a.created,\'%d-%m-%Y %H:%I\') AS fecha_creacion,a.active,b.id_grp,c.description AS grupo FROM sist_users AS a LEFT JOIN sist_multigroup AS b LEFT JOIN sist_grp AS c ON b.id_grp = c.id_grp ON a.id_user = b.id_user GROUP BY id_user ORDER BY a.created DESC, a.id_user DESC LIMIT ?,?', $paginador['offset'], $paginador['limit']);
    foreach ($aRes as $a) {
        if ($a['active'] == 0) {
            $trclass = ' style="background:#EEEF95"';
            $a['active'] = '';
        } else {
            $trclass = '';
            $a['active'] = ' checked="checked"';
        }
        if (empty($a['first_name'])) {
            $a['first_name'] = '';
        }
        if (empty($a['last_name'])) {
            $a['last_name'] = '';
        }
        if (empty($a['grupo'])) {
            $a['grupo'] = $app->he->c_tag('strong', $app->he->c_tag('em', 'Ninguno'));
        }
        $pantalla .= '<tr' . $trclass . '><td class="centrar">' . $a['id_user'] . '</td><td><input type="text" id="usu_' . $a['id_user'] . '" value="' . $a['login'] . '" /></td><td><input type="text" id="nom_' . $a['id_user'] . '" value="' . $a['first_name'] . '" /><br /><input type="text" id="ape_' . $a['id_user'] . '" value="' . $a['last_name'] . '" /></td><td class="centrar">' . $a['fecha_creacion'] . '</td><td><input type="hidden" id="gru_' . $a['id_user'] . '" value="' . $a['id_grp'] . '" />' . $app->he->c_href($app->myHome . 'detalles/' . $a['id_user'], $app->he->c_img(IMAG . 'detalles.png'), 'Detalle de Grupos') . '&nbsp;' . $app->he->c_href($app->myHome . 'detalles/' . $a['login'] . '/', $a['grupo'], 'Detalle de grupos') . '</td><td class="centrar"><input type="checkbox" value="1" id="act_' . $a['id_user'] . '"' . $a['active'] . ' /></td><td class="centrar">' . $app->he->c_img('im/rojo.png', 'Resetear Contrase&ntilde;a', 'puntero', '', 'onclick="javascript:ex(\'r\',' . $a['id_user'] . ');"') . '&nbsp;' . $app->he->c_img('im/edit.png', 'Guardar Cambios', 'puntero', '', 'onclick="javascript:ex(\'m\',' . $a['id_user'] . ');"') . '</td></tr>';
    }
} else {
    if (empty($_GET['b'])) {
        $app->msgStack->add(2, 'Selecci&oacute;n inv&aacute;lida. Por favor intente nuevamente');
        $app->misc->redir($app->myHome);
    } else {
        $id_usuario = $_GET['b'];
        //if (!is_numeric($_GET['a'])) $app->misc->redir($app->myHome.'&err=71');
        //else $id_usuario = $_GET['a'];
    }

    $aRes = $app->db->query('SELECT id_user,first_name,last_name,active FROM sist_users WHERE login = ?', $id_usuario);
    if ($app->db->num_rows > 0) {
        $app->javascriptCode[] = 'function ex(){var c=0;if($("#act_' . $id_usuario . '").is(":checked")){c=1;};$("#activo").val(c);};';
        $r['onload'] = '$(\'#nombre_usuario\').focus();';
        $aRes = $aRes[0];
        $activo = '';
        if (empty($aRes['active']))
            $app->msgStack->add(2, 'Esta cuenta est&aacute; inactiva!');
        else
            $activo = ' checked="checked"';
        if (empty($aRes['nombre']))
            $aRes['nombre'] = '';
        if (empty($aRes['apellido']))
            $aRes['apellido'] = '';
        $pantalla .= '<table class="sin-borde"><tr><td>Login<input type="hidden" name="accion" value="m" /><input type="hidden" name="num_mod" value="' . $id_usuario . '" /><input type="hidden" name="grupo" value="0" /></td><td class="centrar"><input type="text" name="nombre_usuario" id="nombre_usuario" value="' . $id_usuario . '" /></td></tr>';
        $pantalla .= '<tr><td>Nombre</td><td class="centrar"><input type="text" name="nombre" value="' . $aRes['first_name'] . '" /></td></tr>';
        $pantalla .= '<tr><td>Apellido</td><td class="centrar"><input type="text" name="apellido" value="' . $aRes['last_name'] . '" /></td></tr>';
        $pantalla .= '<tr><td>Activo</td><td class="centrar"><input type="checkbox" id="act_' . $id_usuario . '" value=""' . $activo . ' /><input type="hidden" name="activo" id="activo" value="1" /></td></tr>';
        $pantalla .= '<tr><td colspan="2">Pertenencia a Grupos:<input type="hidden" name="grupos[]" value="" /></td></tr><tr><td colspan="2"><table>';
        $aPertenencia = $app->db->query('SELECT id_grp FROM sist_multigroup WHERE id_user = ?', $aRes['id_user']);
        $aGrp = array();
        if ($app->db->num_rows > 0) {
            foreach ($aPertenencia as $a) {
                $aGrp[] = $a['id_grp'];
            }
        }
        foreach ($aGrupos as $a) {
            if (in_array($a['id_grp'], $aGrp)) {
                $checked = ' checked="checked"';
            } else {
                $checked = '';
            }
            $pantalla .= '<tr style="border:1px solid #000"><td style="border:1px solid #000">' . $a['description'] . '</td><td class="centrar" style="border:1px solid #000"><input type="checkbox" value="' . $a['id_grp'] . '"' . $checked . ' name="grupos[]" /></td></tr>';
        }
        $pantalla .= '</table></td></tr><tr><td colspan="2" class="centrar"><input type="submit" value="Guardar Cambios" onclick="javascript:ex();" /></td></tr></table>';
    } else {
        $app->misc->redir($app->myHome . '&err=71');
    }
}
$pantalla .= '</tbody></table></form>';
echo $pantalla;
