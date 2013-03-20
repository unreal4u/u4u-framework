<?php
/**
 * Página cambio de contraseña
 *
 * @package General
 * @author Camilo Sperberg
 */
$r['onload'] = '$("#antigua").select().focus();';
$app->bc->add($app->myHome, __('Change password'));
if (!empty($_POST['cambia_pass'])) {
    if (!empty($_POST['antigua']) and !empty($_POST['pass_nueva1']) and !empty($_POST['pass_nueva2'])) {
        if ($_POST['pass_nueva1'] == $_POST['pass_nueva2']) {
            $_POST['antigua'] = trim(htmlentities($_POST['antigua']));
            $_POST['pass_nueva1'] = trim(htmlentities($_POST['pass_nueva1']));
            $aRes = $app->db->query('SELECT passwd,salt_hash FROM sist_users WHERE id_user = ? AND active = ? LIMIT 1', $r['id_user'], 1);
            if ($app->db->num_rows > 0) {
                $oldPasswd = md5(substr($_POST['antigua'], 0, round(strlen($_POST['antigua']) / 2)) . $aRes[0]['salt_hash'] . substr($_POST['antigua'], round(strlen($_POST['antigua']) / 2)));
                if ($oldPasswd != $aRes[0]['passwd'])
                    $app->msgStack->add(2, 'La contrase&ntilde;a antigua no coincide. Por favor intente nuevamente');
                else {
                    $passwd = $_POST['pass_nueva1'];
                    $hash = md5(uniqid(time() + microtime(), TRUE));
                    $newPasswd = md5(substr($passwd, 0, round(strlen($passwd) / 2)) . $hash . substr($passwd, round(strlen($passwd) / 2)));
                    $app->db->query('UPDATE sist_users SET passwd = ?,salt_hash = ?, created = created WHERE id_user = ?', $newPasswd, $hash, $r['id_user']);
                    $app->misc->logActivity($r['id_user'], 'pwd', __('Password change'));
                    $app->msgStack->add(3, __('Your password has successfully changed'));
                }
            }
        } else
            $app->msgStack->add(2, 'La confirmaci&oacute;n de la nueva contrase&ntilde;a no coincide. Por favor intente nuevamente.');
    } else {
        $app->msgStack->add(2, 'Por favor complete todos los campos');
        $app->misc->redir(HOME.$app->myHome);
    }
}
echo $app->misc->c_title('Cambiar Contrase&ntilde;a', 'En esta p&aacute;gina puede cambiar su contrase&ntilde;a de ingreso al sistema');
?><form action="<?php
echo $_SERVER['REQUEST_URI'];
?>" method="post">
	<table class="sin-borde">
		<tr>
			<td>Contrase&ntilde;a antigua:</td>
			<td><input type="password" name="antigua" id="antigua" value="" /></td>
		</tr>
		<tr>
			<td>Contrase&ntilde;a nueva:</td>
			<td><input type="password" name="pass_nueva1" value="" /></td>
		</tr>
		<tr>
			<td>Confirme:</td>
			<td><input type="password" name="pass_nueva2" value="" /></td>
		</tr>
		<tr>
			<td class="centrar" colspan="2"><input type="submit"
				value="Cambiar Contrase&ntilde;a" name="cambia_pass" /></td>
		</tr>
	</table>
</form><?php
echo $app->he->c_javascript('$(document).ready(function(){$("#antigua").val("");});');
