<?php

$app->isPublicPage = true;
$app->misc->c_title(__('System installation'), __('This page will execute a serie of steps in order to install the system'));

if (!empty($app->options['installed'])) {
    $app->misc->redir('no-permission/');
} else {
    if (!empty($_POST['admin_name'])) {
        $passwd_info = $app->misc->createPassword('345345');
        $id_user = $app->db->insert_id('INSERT INTO sist_users (login,passwd,first_name,last_name,salt_hash) VALUES (?,?,?,?,?)', 'unreal4u', $passwd_info['passwd'], 'Camilo', 'Sperberg', $passwd_info['hash']);
        if (!empty($id_user)) {
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'sitename', 'Framework U4U', $id_user);
            $everyone = $app->db->insert_id('INSERT INTO sist_grp (description) VALUES (?)', 'Todos');
            $id_grp = $app->db->insert_id('INSERT INTO sist_grp (description) VALUES (?)', 'Super-Administrador');
            $app->db->query('INSERT INTO sist_multigroup (id_user,id_grp) VALUES (?,?)', $id_user, $id_grp);
            $app->db->query('INSERT INTO sist_multigroup (id_user,id_grp) VALUES (?,?)', $id_user, $everyone);
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'active_theme', 'default', $id_user);
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'installed', '1', $id_user);
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'jquery_main', 'jquery-1.6.1.min.js', $id_user);
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'mail_smtp_serv', '', $id_user);
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'mail_smtp_port', '', $id_user);
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'mail_smtp_user', '', $id_user);
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'mail_smtp_pass', '', $id_user);
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'mail_from_mail', '', $id_user);
            $app->db->query('INSERT INTO sist_options (id_option,name,v,id_user) VALUES (?,?,?,?)', 'sop', 'mail_from_name', '', $id_user);
            $app->db->query('INSERT INTO sist_menu (description,link,id_grp,id_order) VALUES (?,?,?,?)', 'Listado de Errores', 'index/errors', $id_grp, 100);
            $app->db->query('INSERT INTO sist_menu (description,link,id_grp,id_order) VALUES (?,?,?,?)', 'Detalle Tr&aacute;fico', 'index/detalle-trafico', $id_grp, 110);
            $app->db->query('INSERT INTO sist_menu (description,link,id_grp,id_order) VALUES (?,?,?,?)', 'Administrador - Usuarios', 'index/admin-users', $id_grp, 130);
            $app->db->query('INSERT INTO sist_menu (description,link,id_grp,id_order) VALUES (?,?,?,?)', 'Administrador - Grupos', 'index/admin-grp', $id_grp, 140);
            $app->db->query('INSERT INTO sist_menu (description,link,id_grp,id_order) VALUES (?,?,?,?)', 'Administrador - Men&uacute;', 'index/admin-menu', $id_grp, 150);
            $app->db->query('INSERT INTO sist_menu (description,link,id_grp,id_order) VALUES (?,?,?,?)', 'Inicio', 'admin/', $everyone, 10);
            $app->db->query('INSERT INTO sist_menu (description,link,id_grp,id_order) VALUES (?,?,?,?)', 'Cambiar Contrase&ntilde;a', 'passwd/', $everyone, 99999990);
            $app->db->query('INSERT INTO sist_menu (description,link,id_grp,id_order) VALUES (?,?,?,?)', 'Salir', 'logout/', $everyone, 99999999);
            $app->msgStack->add(3, 'Base de datos exitosamente importada! ' . $app->he->c_href(HOME, 'Volver al Inicio'));
        }
    } else {
        $screen = '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="ppal"><table>';
        $screen .= '<tr><td>Your desired user name:</td><td><input type="admin_name" value="admin" /></td></tr>';
        $screen .= '</table></form>';
        echo $screen;
    }
}
