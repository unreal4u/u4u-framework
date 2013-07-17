<?php
/**
 * Administrador de Menú
 *
 * @package General
 * @author Camilo Sperberg
 */
$app->view->pageTitle = _('Menu administrator');
$app->javascriptCode[] = 'function ex(a,b){var s=true;if(a==\'d\'){s=confirm(\'Confirma que desea eliminar este registro?\');};if(s==true){$("#num_mod").val(b);$("#accion").val(a);$("#grupo").val($("#grp_"+b).val());$("#link").val($("#lnk_"+b).val());$("#descripcion").val($("#dsc_"+b).val());$("#orden").val($("#ord_"+b).val());if($("#vis_"+b).is(":checked")){$("#visible").val(1);}else{$("#visible").val(0);};$("#ppal").submit();};};';
echo $app->misc->c_title($app->view->pageTitle, 'Esta p&aacute;gina sirve para administrar los men&uacute;es del sistema');
if (isset($_POST['accion'])) {
    $_POST['link'] = trim($_POST['link'], '/');
    $_POST['link'] = $_POST['link'] . '/';
    switch ($_POST['accion']) {
        case 'a':
            $consulta = 'INSERT INTO sist_menu (id_grp,link,description,id_order,visible) VALUES (' . $_POST['grupo'] . ',\'' . $_POST['link'] . '\',\'' . $_POST['descripcion'] . '\',' . $_POST['orden'] . ',' . $_POST['visible'] . ')';
        break;
        case 'm':
            $consulta = 'UPDATE sist_menu SET id_grp=' . $_POST['grupo'] . ',link=\'' . $_POST['link'] . '\',description=\'' . $_POST['descripcion'] . '\',id_order=' . $_POST['orden'] . ',visible=' . $_POST['visible'] . ' WHERE id_menu=' . $_POST['num_mod'];
        break;
        case 'd':
            $consulta = 'DELETE FROM sist_menu WHERE id_menu=' . $_POST['num_mod'];
        break;
    }
    if (!empty($consulta)) {
        $app->db->query($consulta);
        $app->msgStack->add(3, 'Registro actualizado correctamente!');
    } else {
        $app->msgStack->add(1, 'Hubo un problema actualizando el registro, por favor notif&iacute;queselo a un administrador!');
    }

    $app->misc->redir($app->myHome);
}
$aGrupos = $app->db->query('SELECT id_grp,description FROM sist_grp ORDER BY created');
$pantalla = '<form id="ppal" action="' . $_SERVER['REQUEST_URI'] . '" method="post"><table><thead><tr><th>Grupo</th><th>Link</th><th>&nbsp;</th><th>Texto</th><th>Orden</th><th>Visible</th><th>Acciones</th></thead><tbody>';
$aRes = $app->db->query('SELECT id_menu,description,link,id_grp,id_order,visible FROM sist_menu ORDER BY id_grp,id_order');
if ($app->db->num_rows > 0) {
    foreach ($aRes as $a) {
        if ($a['visible'] == 1) {
            $a['visible'] = ' checked="checked"';
            $a['txt_visible'] = $app->he->c_img('im/ok.png', 'Es visible');
            $trclass = '';
        } else {
            $a['visible'] = '';
            $a['txt_visible'] = $app->he->c_img('im/nok.png', 'NO es visible');
            $trclass = ' style="background:#FFC"';
        }
        $pantalla .= '<tr' . $trclass . '><td><select id="grp_' . $a['id_menu'] . '">';
        foreach ($aGrupos as $b) {
            if ($b['id_grp'] == $a['id_grp'])
                $selected = ' SELECTED';
            else
                $selected = '';
            $pantalla .= '<option value="' . $b['id_grp'] . '"' . $selected . '>' . $b['description'] . '</option>';
        }
        $pantalla .= '</select></td><td><input type="text" id="lnk_' . $a['id_menu'] . '" value="' . $a['link'] . '" /></td><td class="centrar"><a href="' . HOME . $a['link'] . '">' . $app->he->c_img('im/html.png') . '</a></td><td><input type="text" id="dsc_' . $a['id_menu'] . '" value="' . $a['description'] . '" /></td><td><input type="text" class="chico" id="ord_' . $a['id_menu'] . '" value="' . $a['id_order'] . '" maxlength="5" /></td><td class="centrar"><input type="checkbox" id="vis_' . $a['id_menu'] . '"' . $a['visible'] . ' /></td><td class="centrar">' . $app->he->c_img('im/edit.png', 'Editar', 'puntero', '', 'onclick="javascript:ex(\'m\',' . $a['id_menu'] . ');"') . '&nbsp;' . $app->he->c_img('im/trash.png', 'Borrar', 'puntero', '', 'onclick="javascript:ex(\'d\',' . $a['id_menu'] . ');"') . '</td></tr>';
    }
}
$pantalla .= '<tr><td colspan="7"><strong>Ingresar nuevo</strong></td></tr>';
$pantalla .= '<tr><td><select id="grp_a">';
foreach ($aGrupos as $b)
    $pantalla .= '<option value="' . $b['id_grp'] . '">' . $b['description'] . '</option>';
$pantalla .= '</select></td><td><input type="text" value="" id="lnk_a" /></td><td>&nbsp;</td><td><input type="text" id="dsc_a" value="" /></td><td><input type="text" class="chico" id="ord_a" value="" /></td><td class="centrar"><input type="checkbox" checked="checked" id="vis_a" value="1" /></td><td class="centrar"><input type="button" value="Ingresar" onclick="javascript:ex(\'a\',\'a\');" /><input type="hidden" id="grupo" name="grupo" value="0" /><input type="hidden" id="link" name="link" value="0" /><input type="hidden" name="descripcion" id="descripcion" value="0" /><input type="hidden" id="orden" name="orden" value="0" /><input type="hidden" id="visible" name="visible" value="1" /><input type="hidden" id="accion" name="accion" value="0" /><input type="hidden" id="num_mod" name="num_mod" value="0" /></td></tr>';
$pantalla .= '</tbody></table></form>';
echo $pantalla;
