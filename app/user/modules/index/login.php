<?php
$app->isPublicPage = true;
$app->bc->add($app->myHome, __('Login'));
if ($app->loggedIn) {
    $app->misc->redir(HOME . 'admin/');
}

if (!empty($_POST['user_input']) and !empty($_POST['passwd_input'])) {
    $usuario = trim(htmlentities($_POST['user_input']));
    $passwd  = trim(htmlentities($_POST['passwd_input']));
    $err     = 0;
    $aHash = $app->db->query('SELECT id_user,salt_hash,passwd,first_name,last_name FROM sist_users WHERE login = ? LIMIT ?', $usuario, 1);
    if ($app->db->num_rows > 0) {
        $hash = $aHash[0]['salt_hash'];
        $tmpPasswd = md5(substr($passwd, 0, round(strlen($passwd) / 2)) . $hash . substr($passwd, round(strlen($passwd) / 2)));
        if ($tmpPasswd != $aHash[0]['passwd']) {
            $err = 2;
        }
    } else {
        $err = 1;
    }
    if (empty($err)) {
        $_SESSION['id_user']       = $aHash[0]['id_user'];
        $_SESSION['first_name']    = $aHash[0]['first_name'];
        $_SESSION['last_name']     = $aHash[0]['last_name'];
        $_SESSION['loginUsername'] = $usuario;
        $_SESSION['loggedIn']      = true;
        $aGrp = $app->db->query('SELECT id_grp FROM sist_multigroup WHERE id_user = ?', $_SESSION['id_user']);
        if ($app->db->num_rows > 0) {
            foreach ($aGrp as $a) {
                $_SESSION['id_grp'][] = $a['id_grp'];
            }
        } else {
            $_SESSION['id_grp'] = array();
        }
        $_SESSION['timeout'] = time() + SESION_EXPIRE;
        $app->misc->logActivity($_SESSION['id_user'], 'loi', $_SERVER['REMOTE_ADDR'] . ' - ' . $_SERVER['HTTP_USER_AGENT']);
        $app->misc->redir(HOME . 'admin/');
    } else {
        switch ($err) {
            case 1:
                $app->msgStack->add(2, __('User not found or password not equal. Please try again.'));
            break;
            case 2:
                $app->msgStack->add(2, __('Password not equal. Please try again.'));
            break;
            default:
                $app->msgStack->add(1, sprintf(__('An unknown error happened (%d). Please notify the sysadmin.'), $err));
            break;
        }
    }
}
if (empty($r['username'])) {
    $r['onload'] = '$(\'#user_input\').focus();';
} else {
    $r['onload'] = '$(\'#passwd_input\').focus();';
}
echo $app->he->c_tag('p', __('Please enter your credentials to continue.'));
if (empty($_POST['username'])) {
    $_POST['username'] = '';
}
?><form id="ppal" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<table class="no-border">
		<tr>
			<td><?php echo __('Username'); ?></td>
			<td><input type="text" name="user_input" id="user_input"
				value="<?php echo $_POST['username']; ?>" style="width: 125px" /></td>
		</tr>
		<tr>
			<td><?php echo __('Password'); ?></td>
			<td><input type="password" name="passwd_input" id="passwd_input"
				value="" style="width: 125px" /></td>
		</tr>
		<tr>
			<td colspan="2" class="centrar"><input type="submit"
				value="<?php echo __('Enter'); ?>" style="width: 125px" /></td>
		</tr>
	</table>
</form><?php
unset($r['username']);
